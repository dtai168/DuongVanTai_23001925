// Component <dong-gio-hang>: hiển thị một dòng trong giỏ hàng.
// Không sửa props; mọi thay đổi được gửi lên cha qua $emit.
const DongGioHang = {
  props: {
    sanPham: { type: Object, required: true } // { id, ten, donGia, soLuong }
  },
  emits: {
    'doi-so-luong': (id, soLuongMoi) => id !== undefined && typeof soLuongMoi === 'number',
    'yeu-cau-xoa': (id) => id !== undefined
  },
  computed: {
    thanhTien() {
      return this.sanPham.donGia * this.sanPham.soLuong;
    }
  },
  methods: {
    dinhDang(so) {
      return so.toLocaleString('vi-VN') + ' đồng';
    },
    tang() {
      this.$emit('doi-so-luong', this.sanPham.id, this.sanPham.soLuong + 1);
    },
    giam() {
      this.$emit('doi-so-luong', this.sanPham.id, this.sanPham.soLuong - 1);
    },
    xoa() {
      this.$emit('yeu-cau-xoa', this.sanPham.id);
    }
  },
  template: `
    <div class="cart-row">
      <div class="cart-cell">{{ sanPham.ten }}</div>
      <div class="cart-cell num">{{ dinhDang(sanPham.donGia) }}</div>
      <div class="cart-cell qty">
        <button @click="giam" :disabled="sanPham.soLuong <= 1">−</button>
        <span>{{ sanPham.soLuong }}</span>
        <button @click="tang" :disabled="sanPham.soLuong >= 99">+</button>
      </div>
      <div class="cart-cell num">{{ dinhDang(thanhTien) }}</div>
      <div class="cart-cell"><button class="del" @click="xoa">Xóa</button></div>
    </div>
  `
};
