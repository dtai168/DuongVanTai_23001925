const { createApp } = Vue;

createApp({
    data() {
        return {
            maSinhVien: '23001925',
            hoTen: 'Dương Văn Tài',
            lop: 'K68A4',
            anhDaiDien: 'assets/avatar.svg',
            diemTrungBinh: 8.5,
            dangHoc: true,
            hienGioiThieu: true,
            gioiThieu: 'Xin chào! Mình là sinh viên ngành Công nghệ thông tin, yêu thích lập trình web và đang tìm hiểu Vue.js.'
        };
    },
    computed: {
        diemHopLe() {
            return typeof this.diemTrungBinh === 'number'
                && Number.isFinite(this.diemTrungBinh)
                && this.diemTrungBinh >= 0
                && this.diemTrungBinh <= 10;
        }
    }
}).mount('#app');
