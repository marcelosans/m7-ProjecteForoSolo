document.addEventListener("DOMContentLoaded", function () {
    const usernameInput = document.getElementById("username");
    const saveButton = document.getElementById("save-btn");
    const msg = document.getElementById("username-msg");
    const profileUpload = document.getElementById("profile-upload");
    const bannerUpload = document.getElementById("banner-upload");

    // Verificación del nombre de usuario
    if (usernameInput) {
        usernameInput.addEventListener("input", function () {
            let username = this.value.trim();

            if (username.length > 0) {
                fetch("check_username.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "username=" + encodeURIComponent(username),
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.exists) {
                            msg.textContent = "Nombre de usuario ya en uso";
                            msg.style.color = "red";
                            saveButton.disabled = true;
                        } else {
                            msg.textContent = "Nombre de usuario disponible";
                            msg.style.color = "green";
                            saveButton.disabled = false;
                        }
                    })
                    .catch((error) => console.error("Error:", error));
            } else {
                msg.textContent = "";
                saveButton.disabled = true;
            }
        });
    }

    // Cargar banner de perfil si ya existe
    const profileBanner = document.getElementById("profile-banner");
    if (profileBanner && profileBanner.dataset.banner) {
        profileBanner.style.backgroundImage = `url("${profileBanner.dataset.banner}")`;
    }

    // Subir imagen de perfil
    if (profileUpload) {
        profileUpload.addEventListener("change", function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById("profile-img").src = e.target.result;
                    document.getElementById("image_base64").value = e.target.result.split(",")[1];
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Subir imagen del banner
    if (bannerUpload) {
        bannerUpload.addEventListener("change", function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById("banner-img").src = e.target.result;
                    document.getElementById("banner_base64").value = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
