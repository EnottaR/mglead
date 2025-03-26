<div class="sezione-impostazioni">
    <div class="settings-row">
        <div class="settings-column-left">Nome:</div>
        <div class="settings-column-right">
            <?= htmlspecialchars($_SESSION['name'] ?? 'Ospite'); ?>
        </div>
    </div>
    <div class="settings-row">
        <div class="settings-column-left">Cognome:</div>
        <div class="settings-column-right">
            <?= htmlspecialchars($_SESSION['surname'] ?? 'Ospite'); ?>
        </div>
    </div>
    <div class="settings-row">
        <div class="settings-row">
            <div class="settings-column-left">Indirizzo email:
                <p class="settings-desc">Aggiorna il tuo indirizzo email</p>
            </div>
            <div class="settings-column-right">
                <div id="email-display">
                    <span id="current-email">
                        <?= htmlspecialchars($_SESSION['email'] ?? ''); ?>
                    </span>
                    <button id="edit-email-btn" class="btn-secondary"><i class="far fa-edit"></i>
                        Aggiorna</button>
                </div>
                <div id="email-edit-container" style="display: none; margin-top: 10px;">
                    <input type="email" id="user-email" class="settings-input">
                    <button id="save-email-btn" class="btn-secondary"><i
                            class="fa-regular fa-floppy-disk"></i> Salva</button>
                    <button id="cancel-email-btn" class="btn-primary"><i class="fas fa-times"></i>
                        Annulla</button>
                </div>
            </div>

        </div>
    </div>
    <div class="settings-row">
        <div class="settings-column-left">Password:
            <p class="settings-desc">Ricorda di usare una combinazione sicura</p>
        </div>
        <div class="settings-column-right">
            <div class="password-container">
                <button id="edit-password-btn" class="btn-secondary">
                    <i class="far fa-edit"></i> Cambia password
                </button>
                <div id="password-edit-container" style="display: none; margin-top: 10px;">
                    <input type="password" id="user-password" placeholder="Nuova password"
                        class="settings-input">
                    <button id="save-password-btn" class="btn-secondary"><i class="fas fa-save"></i>
                        Salva</button>
                    <button id="cancel-password-btn" class="btn-primary"><i class="fas fa-times"></i>
                        Annulla</button>
                </div>
            </div>
        </div>
    </div>

    <div class="settings-row">
        <div class="settings-column-left">Azienda:
            <p class="settings-desc">Inserisci il nome della tua attività</p>
        </div>
        <div class="settings-column-right">
            <div id="company-display" style="display: flex; align-items: center;">
                <span id="current-company"><?= htmlspecialchars($company_name ?? ''); ?></span>
                <button id="edit-company-btn" class="btn-secondary" style="margin-left: 10px;">
                    <i class="far fa-edit"></i> Aggiorna
                </button>
            </div>
            <div id="company-edit-container" style="display: none; margin-top: 10px;">
                <input type="text" id="user-company" class="settings-input">
                <button id="save-company-btn" class="btn-secondary">
                    <i class="fa-regular fa-floppy-disk"></i> Salva
                </button>
                <button id="cancel-company-btn" class="btn-primary">
                    <i class="fas fa-times"></i> Annulla
                </button>
            </div>
        </div>
    </div>



    <div class="settings-row">
        <div class="settings-column-left">Nome del tuo sito:
            <p class="settings-desc">Inserisci il nome del tuo sito web</p>
        </div>
        <div class="settings-column-right">
            <div id="website-name-display" style="display: flex; align-items: center;">
                <span id="current-website-name"><?= htmlspecialchars($website_name ?? ''); ?></span>
                <button id="edit-website-name-btn" class="btn-secondary" style="margin-left: 10px;">
                    <i class="far fa-edit"></i> Aggiorna
                </button>
            </div>
            <div id="website-name-edit-container" style="display: none; margin-top: 10px;">
                <input type="text" id="user-website-name" class="settings-input">
                <button id="save-website-name-btn" class="btn-secondary">
                    <i class="fa-regular fa-floppy-disk"></i> Salva
                </button>
                <button id="cancel-website-name-btn" class="btn-primary">
                    <i class="fas fa-times"></i> Annulla
                </button>
            </div>
        </div>
    </div>

    <div class="settings-row">
        <div class="settings-column-left">URL del tuo sito:
            <p class="settings-desc">Inserisci l'URL completo del tuo sito web</p>
        </div>
        <div class="settings-column-right">
            <div id="website-url-display" style="display: flex; align-items: center;">
                <span id="current-website-url"><?= htmlspecialchars($website_url ?? ''); ?></span>
                <button id="edit-website-url-btn" class="btn-secondary" style="margin-left: 10px;">
                    <i class="far fa-edit"></i> Aggiorna
                </button>
            </div>
            <div id="website-url-edit-container" style="display: none; margin-top: 10px;">
                <input type="url" id="user-website-url" class="settings-input">
                <button id="save-website-url-btn" class="btn-secondary">
                    <i class="fa-regular fa-floppy-disk"></i> Salva
                </button>
                <button id="cancel-website-url-btn" class="btn-primary">
                    <i class="fas fa-times"></i> Annulla
                </button>
            </div>
        </div>
    </div>

    <div class="settings-row">
        <div class="settings-column-left">
            <button id="logout-button" class="btn-primary"><i
                    class="fa-solid fa-arrow-right-from-bracket"></i> Esci</button>
            <p class="settings-desc" style="color: #e03f3f">Chiudi la sessione, dovrai<br>effettuare nuovamente l'accesso.</p>
        </div>
    </div>
</div>