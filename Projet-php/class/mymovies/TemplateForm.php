<?php
class TemplateForm {


    
    public static function formPeople() {
        return <<<HTML
        <div id="popupPeople" style="display:none">
            <h3 id="titleFormPeople"></h3>
            <form id="directorForm">
                <label for="lastName">Nom:</label>
                <input type="text" id="lastName" name="lastName" required>

                <label for="firstName">Prénom:</label>
                <input type="text" id="firstName" name="firstName" required>

                <label for="file">Photo:</label>
                <input type="file" id="photoPeople" name="file" accept="image/png" required>

                <button id="buttonFormPeople">Ajouté</button>
                <button type="button" onclick="resetPopupPeople()">Annulé</button>
            </form>
        </div>
HTML;
    }
}
