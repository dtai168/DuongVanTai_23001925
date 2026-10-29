const { createApp } = Vue;

createApp({
    data() {
        return {
            tenCongViecMoi: '',
            boLoc: 'tat-ca',
            loiNhap: '',
            idTiepTheo: 4,
            danhSachCongViec: [
                { id: 1, ten: 'Ôn tập các directive trong Vue.js', hoanThanh: true },
                { id: 2, ten: 'Hoàn thành bài tập quản lý công việc', hoanThanh: false },
                { id: 3, ten: 'Chuẩn bị nội dung cho buổi học tiếp theo', hoanThanh: false }
            ]
        };
    },
    computed: {
        tongSoCongViec() {
            return this.danhSachCongViec.length;
        },
        soViecChuaHoanThanh() {
            return this.danhSachCongViec.filter(congViec => !congViec.hoanThanh).length;
        },
        congViecDaLoc() {
            if (this.boLoc === 'chua-hoan-thanh') {
                return this.danhSachCongViec.filter(congViec => !congViec.hoanThanh);
            }
            if (this.boLoc === 'da-hoan-thanh') {
                return this.danhSachCongViec.filter(congViec => congViec.hoanThanh);
            }
            return this.danhSachCongViec;
        }
    },
    methods: {
        themCongViec() {
            const ten = this.tenCongViecMoi.trim();
            if (!ten) {
                this.loiNhap = 'Vui lòng nhập tên công việc, không chỉ gồm khoảng trắng.';
                return;
            }

            this.danhSachCongViec.push({
                id: this.idTiepTheo++,
                ten: ten,
                hoanThanh: false
            });
            this.tenCongViecMoi = '';
            this.loiNhap = '';
        },
        xoaCongViec(id) {
            this.danhSachCongViec = this.danhSachCongViec.filter(congViec => congViec.id !== id);
        },
        xoaDaHoanThanh() {
            this.danhSachCongViec = this.danhSachCongViec.filter(congViec => !congViec.hoanThanh);
        }
    }
}).mount('#app');
