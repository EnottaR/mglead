document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll(".status-select").forEach((select) => {
        updateStatusColor(select);

        select.addEventListener("change", function () {
            const leadId = this.getAttribute("data-lead-id");
            const newStatus = this.value;

            fetch("includes/update-status.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ lead_id: leadId, status_id: newStatus }),
            })
            .then((response) => response.json())
            .then((data) => {
                showNotification(data.message, data.status === "success" ? "success" : "error");
                if (data.status === "success") {
                    updateStatusColor(select);
                }
            })
            .catch(error => {
                console.error("Errore:", error);
                showNotification("⚠️ Errore durante l'aggiornamento dello status.", "error");
            });
        });
    });

    function updateStatusColor(selectElement) {
        selectElement.classList.remove(...selectElement.classList);
        selectElement.classList.add("status-select", "status-" + selectElement.value);
    }

    const downloadCsvBtn = document.getElementById("download-csv");
    if (downloadCsvBtn) {
        downloadCsvBtn.addEventListener("click", function () {
            window.location.href = "includes/functions/csv-export.php";
        });
    }

});
