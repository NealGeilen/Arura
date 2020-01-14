<div class="row">
    <div class="col-md-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>Hallo</h3>

                <p>Welkom {$aUser.User_Username}</p>
            </div>
            <div class="icon">
                <i class="fas fa-user"></i>
            </div>
        </div>
    </div>
    {if $smarty.now|date_format:"%Y-%m-%d" < $aWebsite.Launchdate}
    <div class="col-md-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{$aWebsite.Launchdate|date_format:"%d/%m/%Y"}</h3>

                    <p>Website Launch</p>
                </div>
                <div class="icon">
                    <i class="fas fa-door-open"></i>
                </div>
            </div>
        </div>
    {/if}
    {if $iUserCount != null}
    <div class="col-md-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{$iUserCount}</h3>

                <p>Active gebruikers</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    {/if}
    {if $iUserCount != null}
    <div class="col-md-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>-</h3>

                <p>Active bezoekers</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    {/if}
    {if $iPageCount !== null}
    <div class="col-md-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{$iPageCount}</h3>

                <p>Active Pagina's</p>
            </div>
            <div class="icon">
                <i class="fas fa-globe-europe"></i>
            </div>
        </div>
    </div>
    {/if}
</div>
<div class="row">
    {if $aSecureTables !== null}
    <div class="col-md-6 col-12">
        <div class="card">
            <div class="card-header border-0">
                <h3 class="card-title">Beveiligde Administratie</h3>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-striped table-valign-middle Arura-Table-Mini">
                    <thead>
                    <tr>
                        <th>Administratie</th>
                        <th>Rol</th>
                        <th>Aantal</th>
                        <th>Meer</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$aSecureTables key=$iKey item=aTable}
                    <tr>
                        <td>{$aTable.Table_Name}</td>
                        <td>{if $aTable.Table_Owner_User_Id == $aUser.User_Id}Eigenaar{else}Gebruiker{/if}</td>
                        <td class="text-success">{$aTable.ROWCOUNT}</td>
                        <td>
                            <div class="btn-group text-white">
                                {if $aTable.Table_Owner_User_Id == $aUser.User_Id}<a class="btn btn-primary" href="/{$aArura.dir}/administration?s={$aTable.Table_Id}"><i class="fas fa-cogs"></i></a>{/if}
                                <a class="btn btn-secondary" href="/{$aArura.dir}/administration?t={$aTable.Table_Id}"><i class="fas fa-pen"></i></a>
                            </div>
                        </td>
                    </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {/if}
    {if $aEvents !== null}
    <div class="col-md-6 col-12">
        <div class="card">
            <div class="card-header border-0">
                <h3 class="card-title">Aankomende evenementen</h3>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-striped table-valign-middle Arura-Table-Mini">
                    <thead>
                    <tr>
                        <th>Naam</th>
                        <th>Datums</th>
                        <th>Meer</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$aEvents key=$iKey item=aEvent}
                    <tr>
                        <td>{$aEvent.Event_Name}</td>
                        <td>{$aEvent.Event_Start_Timestamp|date_format:"%H:%M %d-%m-%y"} t/m {$aEvent.Event_End_Timestamp|date_format:"%H:%M %d-%m-%y"}</td>
                        <td class="btn-group btn-group-sm">
                            <a class="btn btn-primary" href="/{$aArura.dir}/winkel/evenementen/beheer?e={$aEvent.Event_Id}"><i class="fas fa-pen"></i></a>
                        </td>
                    </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {/if}
    {if $aPayments !== null}
    <div class="col-md-6 col-12">
        <div class="card">
            <div class="card-header border-0">
                <h3 class="card-title">Betalingen van de afgelopen 24 uur</h3>
            </div>
            <div class="card-body p-0 table-responsive">
                {assign var="iPaymentsAmount" value="0"}
                <table class="table table-striped table-valign-middle Arura-Table-Mini">
                    <thead>
                    <tr>
                        <th>Omschrijving</th>
                        <th>Bedrag</th>
                        <th>Status</th>
                        <th>Meer</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$aPayments key=$iKey item=aPayment}
                    <tr>
                        <td>{$aPayment.Payment_Description}</td>
                        <td>€ {$aPayment.Payment_Amount|number_format:2:",":"."}{$iPaymentsAmount = $iPaymentsAmount + $aPayment.Payment_Amount}</td>
                        <td>{$aPayment.Payment_Status}</td>
                        <td></td>
                    </tr>
                    {/foreach}
                    </tbody>
                    {if $aPayments !== []}
                    <tfoot>
                    <tr>
                        <td><strong>Totaal:</strong></td>
                        <td><strong>€ {$iPaymentsAmount|number_format:2:",":"."}</strong></td>
                    </tr>
                    </tfoot>
                    {/if}
                </table>
            </div>
        </div>
    </div>
    {/if}
</div>
