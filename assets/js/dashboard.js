// Modalità Dark
document.addEventListener("DOMContentLoaded", function () {
    var modeSwitch = document.querySelector(".switch-mode");
    var tooltipText = modeSwitch.querySelector(".tooltip-text");
    var lunaIcon = document.querySelector(".luna");
    var soleIcon = document.querySelector(".sole");
  
    function updateThemeIcons() {
      if (document.documentElement.classList.contains("dark")) {
        lunaIcon.style.display = "none";
        soleIcon.style.display = "inline-block";
      } else {
        soleIcon.style.display = "none";
        lunaIcon.style.display = "inline-block";
      }
    }
  
    // Controlla se l'utente ha già salvato il tema
    if (localStorage.getItem("theme") === "dark") {
      document.documentElement.classList.add("dark");
      tooltipText.textContent = "Passa alla modalità chiara";
      updateThemeIcons();
    }
  
    modeSwitch.addEventListener("click", function () {
      document.documentElement.classList.toggle("dark");
      modeSwitch.classList.toggle("active");
  
      if (document.documentElement.classList.contains("dark")) {
        localStorage.setItem("theme", "dark");
        tooltipText.textContent = "Passa alla modalità chiara";
      } else {
        localStorage.setItem("theme", "light");
        tooltipText.textContent = "Passa alla modalità scura";
      }
  
      updateThemeIcons();
    });
  
    var listView = document.querySelector(".list-view");
    var gridView = document.querySelector(".grid-view");
    var projectsList = document.querySelector(".project-boxes");
  
    listView.addEventListener("click", function () {
      gridView.classList.remove("active");
      listView.classList.add("active");
      projectsList.classList.remove("jsGridView");
      projectsList.classList.add("jsListView");
    });
  
    gridView.addEventListener("click", function () {
      gridView.classList.add("active");
      listView.classList.remove("active");
      projectsList.classList.remove("jsListView");
      projectsList.classList.add("jsGridView");
    });
  
    // Sezione Messaggi
    document
      .querySelector(".messages-btn")
      .addEventListener("click", function () {
        document.querySelector(".messages-section").classList.add("show");
      });
  
    document
      .querySelector(".messages-close")
      .addEventListener("click", function () {
        document.querySelector(".messages-section").classList.remove("show");
      });
  });
  
  document.addEventListener("DOMContentLoaded", function () {
    const sidebarLinks = document.querySelectorAll(".app-sidebar-link");
  
    sidebarLinks.forEach((link) => {
      link.addEventListener("click", function () {
        sidebarLinks.forEach((link) => link.classList.remove("active"));
  
        this.classList.add("active");
      });
    });
  });
  
  // Funzione upload avatar - DA FINIRE
  document.addEventListener("DOMContentLoaded", function () {
    const avatarUpload = document.getElementById("avatar-upload");
    const avatarPreview = document.getElementById("avatar-preview");
  
    avatarUpload.addEventListener("change", function () {
      const file = this.files[0];
  
      if (file) {
        const reader = new FileReader();
  
        reader.onload = function (e) {
          avatarPreview.src = e.target.result;
        };
  
        const img = new Image();
        img.onload = function () {
          if (img.width > 300 || img.height > 300) {
            alert(
              "L'immagine deve avere una dimensione massima di 300x300 pixel."
            );
            avatarUpload.value = "";
          } else {
            reader.readAsDataURL(file);
          }
        };
        img.src = URL.createObjectURL(file);
      }
    });
  });
  // Fine Avatar
  document.addEventListener("DOMContentLoaded", function () {
    var menuToggle = document.getElementById("menu-toggle");
    var sidebar = document.querySelector(".app-sidebar");

    if (menuToggle && sidebar) {
        menuToggle.addEventListener("click", function () {
            sidebar.classList.toggle("active"); // Mostra/nasconde la sidebar
        });

        // Chiude la sidebar se si clicca fuori
        document.addEventListener("click", function (event) {
            if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                sidebar.classList.remove("active");
            }
        });
    }
});

function showNotification(message, type = "success") {
    const notificationContainer = document.getElementById("notification-container");
    const notification = document.createElement("div");
    notification.classList.add("notification", type);
    notification.innerHTML = `${message} <span style="cursor:pointer;">✖</span>`;

    // Rimuovi la notifica manualmente al clic
    notification.querySelector("span").addEventListener("click", () => {
        notification.remove();
    });

    notificationContainer.appendChild(notification);

    // Rimuovi automaticamente dopo 5 secondi
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
