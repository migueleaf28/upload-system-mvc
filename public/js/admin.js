document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("configForm");

    if (form) {
        form.addEventListener("submit", function (e) {
            const userDefaultStorage = document.querySelector(
                'input[name="user_default_storage"]'
            );
            const maxFileSize = document.querySelector(
                'input[name="max_file_size"]'
            );

            console.log("Valores antes de conversión:", {
                userDefaultStorage: userDefaultStorage.value,
                maxFileSize: maxFileSize.value,
            });

            if (userDefaultStorage && userDefaultStorage.value) {
                userDefaultStorage.value =
                    parseInt(userDefaultStorage.value) * 1048576;
            }
            if (maxFileSize && maxFileSize.value) {
                maxFileSize.value = parseInt(maxFileSize.value) * 1048576;
            }

            console.log("Valores después de conversión:", {
                userDefaultStorage: userDefaultStorage.value,
                maxFileSize: maxFileSize.value,
            });
        });
    }

    window.addExtension = addExtension;
});

function addExtension() {
    const extensionInput = document.getElementById("new_extension");
    const extension = extensionInput.value.trim().toLowerCase();

    if (extension) {
        console.log("Agregando extensión:", extension);

        const form = document.createElement("form");
        form.method = "POST";
        form.action = '{{ route("admin.config.add-extension") }}';

        const csrfToken = document.createElement("input");
        csrfToken.type = "hidden";
        csrfToken.name = "_token";
        csrfToken.value = "{{ csrf_token() }}";

        const extensionField = document.createElement("input");
        extensionField.type = "hidden";
        extensionField.name = "extension";
        extensionField.value = extension;

        form.appendChild(csrfToken);
        form.appendChild(extensionField);
        document.body.appendChild(form);
        form.submit();
    } else {
        alert("Por favor ingresa una extensión");
    }
}

document
    .getElementById("new_extension")
    .addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
            e.preventDefault();
            addExtension();
        }
    });
