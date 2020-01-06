<div class="card">
    <header class="card-header">
        <h2 class="card-title">Profiel instellingen</h2>
    </header>
    <div class="card-body">
        <form class="form-sender" action="/{$aArura.dir}/{$aArura.api}/user/update-user.php" method="post" autocomplete="off">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="username-profile">Gebruikersnaam</label>
                    <input type="text" class="form-control" id="username-profile" value="{$aUser.User_Username}" placeholder="Gebruikersnaam" name="User_Username" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="firstname-profile">Voornaam</label>
                    <input type="text" class="form-control" id="firstname-profile" value="{$aUser.User_Firstname}" placeholder="Voornaam" name="User_Firstname" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="lastname-profile">Achternaam</label>
                    <input type="text" class="form-control" id="lastname-profile" value="{$aUser.User_Lastname}" placeholder="Achternaam" name="User_Lastname" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="email-profile">E-mail</label>
                    <input type="email" class="form-control" id="email-profile" value="{$aUser.User_Email}" placeholder="E-mail" name="User_Email" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="password-two-profile">Wachtwoord</label>
                    <input type="password" class="form-control" id="password-two-profile" name="Password1">
                </div>
                <div class="form-group col-md-6">
                    <label for="password-one-profile">Wachtwoord bevestiging</label>
                    <input type="password" class="form-control" id="password-one-profile" name="Password2">
                </div>
            </div>
            <div class="btn-group" role="group">
                <button type="submit" class="btn btn-success">Opslaan</button>
                <button type="reset" class="btn btn-danger">Annuleren</button>
            </div>
        </form>
    </div>
</div>