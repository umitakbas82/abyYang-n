document.addEventListener("DOMContentLoaded", function () {
    console.log("ABY Form Sistemi Hazır! 🛡️");

    // Form Yönetici Fonksiyon
    function setupForm(formId, btnId, btnTextId, loaderId) {
        const formElement = document.getElementById(formId);

        if (formElement) {
            console.log(formId + " bulundu ve dinleniyor...");

            formElement.addEventListener("submit", function (event) {
                event.preventDefault();

                var form = this;
                var btn = document.getElementById(btnId);
                var btnText = document.getElementById(btnTextId);
                var btnLoader = document.getElementById(loaderId);

                // Butonları kilitle ve yükleniyor moduna al
                if (btn) btn.disabled = true;
                if (btnText) btnText.textContent = "GÖNDERİLİYOR...";
                if (btnLoader) btnLoader.classList.remove("d-none");

                var formData = new FormData(form);

                fetch("mail_gonder.php", {
                    method: "POST",
                    body: formData
                })
                    .then(response => response.text())
                    .then(text => {
                        console.log("Sunucu Cevabı:", text);

                        try {
                            const data = JSON.parse(text);

                            if (data.status === "success") {
                                // Başarılı Mesajı (Gold Tema)
                                Swal.fire({
                                    title: 'Teşekkürler!',
                                    text: data.message,
                                    icon: 'success',
                                    iconColor: '#D4AF37',
                                    confirmButtonText: 'TAMAM',
                                    background: '#000',
                                    color: '#fff',
                                    confirmButtonColor: '#D4AF37'
                                });
                                form.reset();
                            } else {
                                // Hata Mesajı
                                Swal.fire({
                                    title: 'Hata Oluştu',
                                    text: data.message,
                                    icon: 'error',
                                    background: '#000',
                                    color: '#fff',
                                    confirmButtonColor: '#D4AF37'
                                });
                            }
                        } catch (e) {
                            // JSON Hatası
                            Swal.fire({
                                title: 'Sunucu Hatası!',
                                text: 'Sunucudan beklenmeyen bir cevap geldi.',
                                icon: 'warning',
                                background: '#000',
                                color: '#fff',
                                confirmButtonColor: '#D4AF37'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Bağlantı Hatası',
                            text: 'Lütfen internet bağlantınızı kontrol edin.',
                            icon: 'error',
                            background: '#000',
                            color: '#fff',
                            confirmButtonColor: '#D4AF37'
                        });
                    })
                    .finally(() => {
                        // Butonları eski haline getir
                        if (btn) btn.disabled = false;
                        if (btnText) btnText.textContent = "GÖNDER";
                        if (btnLoader) btnLoader.classList.add("d-none");
                    });
            });
        }
    }

    // Sadece İletişim Formunu Kur
    setupForm("contactForm", "submitBtn", "btnText", "btnLoader");
});