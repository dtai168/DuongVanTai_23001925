// Component <the-san-pham>: hiển thị một thẻ sản phẩm
const TheSanPham = {
  props: {
    ten: { type: String, required: true },
    gia: { type: Number, required: true },
    anh: { type: String, default: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=600&q=80' },
    moTa: { type: String, default: 'Chưa có mô tả cho sản phẩm này.' },
    soLuongTon: { type: Number, default: 0 }
  },
  data() {
    // Trạng thái riêng của từng thẻ
    return { hienMoTa: false };
  },
  computed: {
    hetHang() {
      return this.soLuongTon <= 0;
    },
    giaHienThi() {
      return this.gia.toLocaleString('vi-VN') + ' đ';
    }
  },
  methods: {
    batTatMoTa() {
      this.hienMoTa = !this.hienMoTa;
    },
    mua() {
      alert(`Đã thêm "${this.ten}" vào giỏ hàng!`);
    }
  },
  template: `
    <div class="card">
      <span v-if="hetHang" class="label-out">Hết hàng</span>
      <img :src="anh" :alt="ten">
      <h3>{{ ten }}</h3>
      <p class="price">{{ giaHienThi }}</p>
      <p class="stock">Tồn kho: {{ soLuongTon }}</p>
      <p v-if="hienMoTa" class="desc">{{ moTa }}</p>
      <div class="actions">
        <button @click="batTatMoTa">{{ hienMoTa ? 'Ẩn mô tả' : 'Xem mô tả' }}</button>
        <button class="buy" :disabled="hetHang" @click="mua">Mua</button>
      </div>
    </div>
  `
};
