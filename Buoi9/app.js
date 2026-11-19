/* Vue Options API: dữ liệu biểu mẫu -> PHP API -> tải lại danh sách từ MySQL. */
if (typeof Vue === 'undefined') {
  document.getElementById('boot-message').textContent =
    'Không tải được Vue.js từ CDN. Hãy kiểm tra kết nối Internet và tải lại trang.';
} else {
  const money = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' });
  const number = new Intl.NumberFormat('vi-VN');
  const INT_MAX = 2147483647;
  const PAGE_SIZE = 5;
  const MAX_IMAGE_BYTES = 2 * 1024 * 1024;

  Vue.createApp({
    data() {
      return {
        products: [],
        pagination: { page: 1, limit: PAGE_SIZE, total: 0, total_pages: 1 },
        summary: { total_stock: 0, out_of_stock: 0 },
        form: { name: '', price: '', stock: 0 },
        editingId: null,
        currentImage: null,
        selectedImage: null,
        previewUrl: '',
        imageError: '',
        uploadError: '',
        pendingUploadId: null,
        uploading: false,
        loading: false,
        saving: false,
        deletingId: null,
        hasLoaded: false,
        error: '',
        notice: ''
      };
    },
    computed: {
      busy() {
        return this.loading || this.saving || this.deletingId !== null;
      },
      imagePreview() {
        return this.previewUrl || this.currentImage;
      }
    },
    methods: {
      formatPrice(value) {
        return money.format(value);
      },
      formatNumber(value) {
        return number.format(value);
      },
      // Cùng một nguồn gốc với trang HTML, chạy được cả trong thư mục con XAMPP.
      async request(url, method = 'GET', body = null) {
        const options = { method, cache: 'no-store', headers: { Accept: 'application/json' } };
        if (body instanceof FormData) {
          options.body = body;
        } else if (body !== null) {
          options.headers['Content-Type'] = 'application/json';
          options.body = JSON.stringify(body);
        }

        let response;
        try {
          response = await fetch(url, options);
        } catch {
          throw new Error('Không kết nối được máy chủ. Hãy mở trang qua máy chủ PHP và thử lại.');
        }

        let result;
        try {
          result = await response.json();
        } catch {
          throw new Error('Máy chủ không trả về JSON hợp lệ. Hãy kiểm tra PHP và đường dẫn API.');
        }
        if (!response.ok || result?.success !== true) {
          throw new Error(result?.message || 'Yêu cầu chưa thực hiện được. Vui lòng thử lại.');
        }
        return result;
      },
      async fetchProducts(page = this.pagination.page) {
        const params = new URLSearchParams({ page, limit: PAGE_SIZE });
        const result = await this.request('api/products.php?' + params);
        if (!Array.isArray(result.data) || !result.pagination || !result.summary) {
          throw new Error('Danh sách sản phẩm trả về không hợp lệ.');
        }
        this.products = result.data;
        this.pagination = result.pagination;
        this.summary = result.summary;
        this.hasLoaded = true;
      },
      async loadProducts(page = this.pagination.page) {
        if (this.busy) return;
        this.error = '';
        this.notice = '';
        this.loading = true;
        try {
          await this.fetchProducts(page);
        } catch (error) {
          this.error = error.message;
        } finally {
          this.loading = false;
        }
      },
      resetForm() {
        this.clearImageSelection();
        this.form = { name: '', price: '', stock: 0 };
        this.editingId = null;
        this.currentImage = null;
        this.pendingUploadId = null;
        this.uploadError = '';
      },
      editProduct(product) {
        if (this.busy || this.pendingUploadId !== null) return;
        this.resetForm();
        this.form = { name: product.name, price: product.price, stock: product.stock };
        this.editingId = product.id;
        this.currentImage = product.image;
        this.error = '';
        this.notice = '';
        this.$nextTick(() => this.$refs.productName.focus());
      },
      clearImageSelection() {
        if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
        this.previewUrl = '';
        this.selectedImage = null;
        this.imageError = '';
        if (this.$refs.productImage) this.$refs.productImage.value = '';
      },
      selectImage(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
        this.previewUrl = '';
        this.selectedImage = null;
        this.imageError = '';
        this.uploadError = '';
        if (!/\.(jpe?g|png)$/i.test(file.name) || (file.type && !['image/jpeg', 'image/png'].includes(file.type))) {
          this.imageError = 'Chỉ chấp nhận ảnh JPG, JPEG hoặc PNG.';
        } else if (file.size === 0 || file.size > MAX_IMAGE_BYTES) {
          this.imageError = 'Ảnh phải có dung lượng lớn hơn 0 và tối đa 2 MB.';
        }
        if (this.imageError) {
          event.target.value = '';
          return;
        }
        this.selectedImage = file;
        this.previewUrl = URL.createObjectURL(file);
      },
      async uploadImage() {
        const body = new FormData();
        body.append('product_id', String(this.pendingUploadId));
        body.append('image', this.selectedImage);
        this.uploading = true;
        this.uploadError = '';
        try {
          await this.request('api/upload.php', 'POST', body);
          this.resetForm();
          return true;
        } catch (error) {
          this.uploadError = error.message;
          return false;
        } finally {
          this.uploading = false;
        }
      },
      async refreshAfterChange(page) {
        try {
          await this.fetchProducts(page);
        } catch (error) {
          this.error = 'Thao tác đã được lưu. Bấm Tải lại danh sách để cập nhật. ' + error.message;
        }
      },
      async retryUpload() {
        if (this.busy || this.pendingUploadId === null || !this.selectedImage) return;
        this.error = '';
        this.saving = true;
        try {
          if (await this.uploadImage()) {
            this.notice = 'Đã tải ảnh sản phẩm.';
            await this.refreshAfterChange(this.pagination.page);
          }
        } finally {
          this.saving = false;
        }
      },
      async saveProduct() {
        if (this.busy) return;
        if (this.pendingUploadId !== null) {
          await this.retryUpload();
          return;
        }
        if (this.imageError) return;
        this.error = '';
        this.notice = '';
        const payload = { name: this.form.name.trim(), price: this.form.price, stock: this.form.stock };
        if (Array.from(payload.name).length < 1 || Array.from(payload.name).length > 100) {
          this.error = 'Tên sản phẩm sau khi bỏ khoảng trắng hai đầu phải có từ 1 đến 100 ký tự.';
          return;
        }
        if (!Number.isInteger(payload.price) || payload.price <= 0 || payload.price > INT_MAX) {
          this.error = 'Đơn giá phải là số nguyên từ 1 đến 2.147.483.647 đồng.';
          return;
        }
        if (!Number.isInteger(payload.stock) || payload.stock < 0 || payload.stock > INT_MAX) {
          this.error = 'Số lượng tồn phải là số nguyên từ 0 đến 2.147.483.647.';
          return;
        }

        this.saving = true;
        const isEditing = this.editingId !== null;
        const page = isEditing ? this.pagination.page : 1;
        try {
          const url = 'api/products.php' + (isEditing ? '?id=' + this.editingId : '');
          const result = await this.request(url, isEditing ? 'PUT' : 'POST', payload);
          this.notice = isEditing ? 'Đã cập nhật sản phẩm.' : 'Đã thêm sản phẩm mới.';
          if (this.selectedImage) {
            this.pendingUploadId = result.data.id;
            if (await this.uploadImage()) this.notice += ' Đã tải ảnh sản phẩm.';
          } else {
            this.resetForm();
          }
          await this.refreshAfterChange(page);
        } catch (error) {
          this.error = error.message;
        } finally {
          this.saving = false;
        }
      },
      async deleteProduct(product) {
        if (this.busy || !window.confirm('Bạn có chắc muốn xóa "' + product.name + '"?')) return;
        this.error = '';
        this.notice = '';
        this.deletingId = product.id;
        try {
          await this.request('api/products.php?id=' + product.id, 'DELETE');
          if (this.editingId === product.id || this.pendingUploadId === product.id) this.resetForm();
          this.notice = 'Đã xóa sản phẩm "' + product.name + '".';
          await this.refreshAfterChange(this.pagination.page);
        } catch (error) {
          this.error = error.message;
        } finally {
          this.deletingId = null;
        }
      }
    },
    mounted() {
      document.getElementById('boot-message').remove();
      this.loadProducts();
    },
    beforeUnmount() {
      if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
    }
  }).mount('#app');
}
