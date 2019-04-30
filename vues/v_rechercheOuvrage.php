
<div id="content">
    <h2>Rechercher un ouvrage</h2>
    <div id="object-list">
        <form action="index.php?uc=rechercheOuvrage&action=validerRecherche" method="post">
            <div class="corps-form">
                <fieldset>
                    <legend>Recherche</legend>
                    <table>
                        <tr>
                            <td>
                                <label for="txtTitre">
                                    Titre : 
                                </label>
                            </td>
                            <td>
                                <input 
                                    type="text" id="txtTitre"
                                    name="txtTitre"
                                    size="50" maxlength="128"
                                />
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </div>
            <div class="pied-form">
                <p>
                    <input id="cmdValider" name="cmdValider"
                            type="submit"
                            value="Recherche"
                    />
                </p>
            </div>
        </form>
    </div>
</div>
