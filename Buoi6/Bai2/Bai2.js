const { createApp } = Vue;

createApp({
    data() {
        return {
            tenPhim: 'Chuyến đi mùa hạ',
            hoTen: '',
            suatChieu: '19:00',
            loaiVe: 'thuong',
            soLuong: 1,
            chonCombo: false,
            xacNhan: null
        };
    },
    computed: {
        giaVe() {
            return this.loaiVe === 'vip' ? 100000 : 70000;
        },
        soLuongHopLe() {
            return Number.isInteger(this.soLuong) && this.soLuong >= 1 && this.soLuong <= 10;
        },
        tienCombo() {
            return this.chonCombo ? 45000 : 0;
        },
        tongTien() {
            return this.soLuongHopLe ? this.giaVe * this.soLuong + this.tienCombo : 0;
        },
        coTheDatVe() {
            return this.hoTen.trim().length > 0 && this.soLuongHopLe;
        }
    },
    methods: {
        dinhDangTien(soTien) {
            return soTien.toLocaleString('vi-VN') + ' đồng';
        },
        tangSoLuong() {
            this.soLuong = this.soLuongHopLe ? Math.min(10, this.soLuong + 1) : 1;
        },
        giamSoLuong() {
            this.soLuong = this.soLuongHopLe ? Math.max(1, this.soLuong - 1) : 1;
        },
        datLai() {
            this.hoTen = '';
            this.suatChieu = '19:00';
            this.loaiVe = 'thuong';
            this.soLuong = 1;
            this.chonCombo = false;
            this.xacNhan = null;
        },
        datVe() {
            // Kiểm tra lại trước khi tạo xác nhận, kể cả khi gọi bằng mã.
            if (!this.coTheDatVe) return;

            this.xacNhan = {
                hoTen: this.hoTen.trim(),
                tenPhim: this.tenPhim,
                suatChieu: this.suatChieu,
                loaiVe: this.loaiVe === 'vip' ? 'Vé VIP' : 'Vé thường',
                soLuong: this.soLuong,
                chonCombo: this.chonCombo,
                tongTien: this.tongTien
            };
        }
    }
}).mount('#app');
