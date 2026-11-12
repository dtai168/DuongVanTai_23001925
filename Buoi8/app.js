/* Vue Options API: dữ liệu biểu mẫu -> PHP API -> tải lại danh sách từ MySQL. */
if (typeof Vue === 'undefined') {
  document.getElementById('boot-message').textContent =
    'Không tải được Vue.js từ CDN. Hãy kiểm tra kết nối Internet và tải lại trang.';
} else {
  const money = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' });
  const number = new Intl.NumberFormat('vi-VN');
  const INT_MAX = 2147483647;

  Vue.createApp({
    data() {
      return {
        products: [],
        form: { name: '', price: '', stock: 0 },
        editingId: null,
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
      totalStock() {
        return this.products.reduce((sum, product) => sum + product.stock, 0);
      },
      outOfStock() {
        return this.products.filter(product => product.stock === 0).length;
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
      async request(method = 'GET', id = null, body = null) {
        const url = 'api/products.php' + (id !== null ? '?id=' + encodeURIComponent(id) : '');
        const options = { method, cache: 'no-store', headers: { Accept: 'application/json' } };
        if (body !== null) {
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
      async fetchProducts() {
        const result = await this.request();
        if (!Array.isArray(result.data)) {
          throw new Error('Danh sách sản phẩm trả về không hợp lệ.');
        }
        this.products = result.data;
        this.hasLoaded = true;
      },
      async loadProducts() {
        if (this.busy) return;
        this.error = '';
        this.notice = '';
        this.loading = true;
        try {
          await this.fetchProducts();
        } catch (error) {
          this.error = error.message;
        } finally {
          this.loading = false;
        }
      },
      resetForm() {
        this.form = { name: '', price: '', stock: 0 };
        this.editingId = null;
      },
      editProduct(product) {
        if (this.busy) return;
        this.form = { name: product.name, price: product.price, stock: product.stock };
        this.editingId = product.id;
        this.error = '';
        this.notice = '';
        this.$nextTick(() => this.$refs.productName.focus());
      },
      async saveProduct() {
        if (this.busy) return;
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
        let saved = false;
        try {
          await this.request(isEditing ? 'PUT' : 'POST', this.editingId, payload);
          saved = true;
          this.resetForm();
          // Luôn GET lại sau khi ghi để bảng phản ánh dữ liệu từ cơ sở dữ liệu.
          await this.fetchProducts();
          this.notice = isEditing ? 'Đã cập nhật sản phẩm.' : 'Đã thêm sản phẩm mới.';
        } catch (error) {
          this.error = saved
            ? 'Đã lưu sản phẩm, nhưng chưa tải lại được danh sách. Bấm Tải lại danh sách để cập nhật. ' + error.message
            : error.message;
        } finally {
          this.saving = false;
        }
      },
      async deleteProduct(product) {
        if (this.busy || !window.confirm('Bạn có chắc muốn xóa "' + product.name + '"?')) return;
        this.error = '';
        this.notice = '';
        this.deletingId = product.id;
        let deleted = false;
        try {
          await this.request('DELETE', product.id);
          deleted = true;
          if (this.editingId === product.id) this.resetForm();
          await this.fetchProducts();
          this.notice = 'Đã xóa sản phẩm "' + product.name + '".';
        } catch (error) {
          this.error = deleted
            ? 'Đã xóa sản phẩm, nhưng chưa tải lại được danh sách. Bấm Tải lại danh sách để cập nhật. ' + error.message
            : error.message;
        } finally {
          this.deletingId = null;
        }
      }
    },
    mounted() {
      document.getElementById('boot-message').remove();
      this.loadProducts();
    }
  }).mount('#app');
}
