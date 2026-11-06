// Component TheSanPham
const TheSanPham = {
  name: 'TheSanPham',
  props: {
    ten: {
      type: String,
      required: true
    },
    gia: {
      type: Number,
      required: true
    },
    anh: {
      type: String,
      default: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&auto=format&fit=crop&q=60'
    },
    moTa: {
      type: String,
      default: 'Chưa có thông tin mô tả chi tiết cho sản phẩm này.'
    },
    soLuongTon: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      // Yêu cầu 5 & 6: Trạng thái xem mô tả lưu trong data riêng của từng component
      dangXemMoTa: false
    };
  },
  methods: {
    toggleMoTa() {
      this.dangXemMoTa = !this.dangXemMoTa;
    },
    dinhDangGia(soTien) {
      return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(soTien);
    }
  },
  template: `
    <div class="col-12 col-md-6 col-lg-3 mb-4">
      <div class="card h-100 shadow-sm border-0 position-relative product-card">
        <!-- Huy hiệu trạng thái -->
        <span 
          v-if="soLuongTon <= 0" 
          class="badge bg-danger position-absolute top-0 end-0 m-2 px-2 py-1">
          Hết hàng
        </span>
        <span 
          v-else 
          class="badge bg-success position-absolute top-0 end-0 m-2 px-2 py-1">
          Còn {{ soLuongTon }} sp
        </span>

        <img :src="anh" class="card-img-top" :alt="ten" style="height: 200px; object-fit: cover;">
        
        <div class="card-body d-flex flex-column">
          <h5 class="card-title text-truncate" :title="ten">{{ ten }}</h5>
          <p class="card-text text-danger fw-bold fs-5 mb-2">{{ dinhDangGia(gia) }}</p>
          
          <!-- Phần mô tả có thể bật/tắt độc lập -->
          <div v-show="dangXemMoTa" class="alert alert-light border small text-muted my-2 p-2 rounded">
            {{ moTa }}
          </div>

          <div class="mt-auto pt-2 d-flex gap-2">
            <!-- Nút bật/tắt xem mô tả -->
            <button 
              type="button" 
              class="btn btn-sm btn-outline-secondary flex-grow-1"
              @click="toggleMoTa">
              {{ dangXemMoTa ? 'Ẩn mô tả' : 'Xem mô tả' }}
            </button>
            
            <!-- Yêu cầu 7: Sản phẩm hết hàng khóa nút Mua -->
            <button 
              type="button" 
              class="btn btn-sm btn-primary flex-grow-1"
              :disabled="soLuongTon <= 0">
              <i class="bi bi-cart-plus me-1"></i>{{ soLuongTon <= 0 ? 'Hết hàng' : 'Mua ngay' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  `
};

// Đăng ký toàn cục để hỗ trợ cả khi mở trực tiếp file:/// lẫn module
if (typeof window !== 'undefined') {
  window.TheSanPham = TheSanPham;
}
