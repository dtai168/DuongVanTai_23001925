// Component DongGioHang
const DongGioHang = {
  name: 'DongGioHang',
  props: {
    id: {
      type: [Number, String],
      required: true
    },
    ten: {
      type: String,
      required: true
    },
    donGia: {
      type: Number,
      required: true
    },
    soLuong: {
      type: Number,
      required: true,
      validator(value) {
        return Number.isInteger(value) && value >= 1 && value <= 99;
      }
    }
  },
  // Yêu cầu 3: Khai báo các sự kiện tùy chỉnh
  emits: ['doi-so-luong', 'yeu-cau-xoa'],
  computed: {
    // Thành tiền = đơn giá * số lượng
    thanhTien() {
      return this.donGia * this.soLuong;
    }
  },
  methods: {
    dinhDangTien(soTien) {
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(soTien);
    },
    // Yêu cầu 3 & 6: Không gán lại prop, chỉ emit sự kiện lên component cha
    tangSoLuong() {
      if (this.soLuong < 99) {
        this.$emit('doi-so-luong', {
          id: this.id,
          soLuongMoi: this.soLuong + 1
        });
      }
    },
    giamSoLuong() {
      if (this.soLuong > 1) {
        this.$emit('doi-so-luong', {
          id: this.id,
          soLuongMoi: this.soLuong - 1
        });
      }
    },
    nhapSoLuong(event) {
      const giaTri = parseInt(event.target.value, 10);
      // Nếu nhập không hợp lệ hoặc ngoài khoảng 1-99 thì reset về giá trị hiện tại của prop
      if (isNaN(giaTri) || giaTri < 1 || giaTri > 99) {
        event.target.value = this.soLuong;
        return;
      }
      this.$emit('doi-so-luong', {
        id: this.id,
        soLuongMoi: giaTri
      });
    },
    xoa() {
      this.$emit('yeu-cau-xoa', this.id);
    }
  },
  template: `
    <tr>
      <td class="align-middle fw-semibold">
        <span class="badge bg-secondary me-2">#{{ id }}</span>{{ ten }}
      </td>
      <td class="align-middle text-end text-nowrap">
        {{ dinhDangTien(donGia) }}
      </td>
      <td class="align-middle text-center" style="width: 180px;">
        <div class="input-group input-group-sm justify-content-center">
          <!-- Nút giảm -->
          <button 
            type="button" 
            class="btn btn-outline-secondary" 
            :disabled="soLuong <= 1"
            @click="giamSoLuong">
            -
          </button>
          
          <!-- Ô nhập số lượng (1 đến 99) -->
          <input 
            type="number" 
            class="form-control text-center" 
            style="max-width: 60px;"
            :value="soLuong"
            @change="nhapSoLuong"
            min="1" 
            max="99">

          <!-- Nút tăng -->
          <button 
            type="button" 
            class="btn btn-outline-secondary" 
            :disabled="soLuong >= 99"
            @click="tangSoLuong">
            +
          </button>
        </div>
      </td>
      <td class="align-middle text-end fw-bold text-primary text-nowrap">
        {{ dinhDangTien(thanhTien) }}
      </td>
      <td class="align-middle text-center" style="width: 80px;">
        <!-- Nút xóa -->
        <button 
          type="button" 
          class="btn btn-sm btn-outline-danger" 
          title="Xóa sản phẩm"
          @click="xoa">
          Xóa
        </button>
      </td>
    </tr>
  `
};

if (typeof window !== 'undefined') {
  window.DongGioHang = DongGioHang;
}
