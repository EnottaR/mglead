document.addEventListener("DOMContentLoaded", function () {
    const notificaBtn = document.getElementById("notifica-btn");
    const notificaBox = document.getElementById("notifica-box");
    const notificaContenuto = document.getElementById("notifica-contenuto");
    const chiudiNotifica = document.getElementById("chiudi-notifica");
    const notificationDot = document.getElementById("new-lead-notification");

    let newLeadsExist = false;

    if (!notificaBtn || !notificaBox || !notificationDot) return;

    function checkNewLeads() {
        fetch("includes/new-lead-alert.php")
            .then(response => response.json())
            .then(data => {
                if (data.status === "success" && data.new_leads > 0) {
                    newLeadsExist = true;

                    const latestLeadTime = parseInt(data.leads[0].created_at);
                    const lastReadTime = localStorage.getItem("lastReadLeadTime");

                    if (!lastReadTime || latestLeadTime > lastReadTime) {
                        notificationDot.style.display = "block";
                    }

                    let leadHtml = "";
                    data.leads.slice(0, 5).forEach(lead => {
                        leadHtml += `<div class="notifica-lead">
                            🟢 Nuovo lead da <span>${lead.name} ${lead.surname}</span><br>
                            <small>${lead.created_at}</small>
                        </div>`;
                    });

                    notificaContenuto.innerHTML = leadHtml;
                } else {
                    newLeadsExist = false;
                    notificationDot.style.display = "none";
                    notificaContenuto.innerHTML = "<p>Non hai nessuna novità.</p>";
                }
            })
            .catch(error => console.error("Errore nel controllo nuovi lead:", error));
    }

    notificaBtn.addEventListener("click", function () {
        notificaBox.classList.toggle("show");
        checkNewLeads();

        if (newLeadsExist) {
            setTimeout(() => {
                notificationDot.style.display = "none";
                newLeadsExist = false;
                localStorage.setItem("lastReadLeadTime", Date.now()); // AGGIORNA SOLO QUANDO L'UTENTE LEGGE
            }, 3000);
        }
    });

    document.addEventListener("click", function (event) {
        if (!notificaBox.contains(event.target) && !notificaBtn.contains(event.target)) {
            notificaBox.classList.remove("show");
        }
    });

    chiudiNotifica.addEventListener("click", function () {
        notificaBox.classList.remove("show");
    });

    setInterval(checkNewLeads, 10000);
    checkNewLeads();
});
