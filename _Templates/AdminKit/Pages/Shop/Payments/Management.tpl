{extends "../../../index.tpl"}
{block breadcrum}
    <li class="breadcrumb-item active">Betalingen</li>
{/block}
{block content}
    <style>
        .payment{
            background: #e7e7e7;
            border: 2px solid gray;
            font-weight: bold;
            color: black;
            text-align: center;
            text-transform: capitalize;
            padding: 3px;
            border-radius: 5px;
        }
        .open{
            border-color: black;
            color: white
        }
        .paid{
            border-color: green;
            color: green;
        }
        .expired{
            border-color: orange;
            color: orange;
        }
        .pending{
            border-color: yellow;
            color: yellow;
        }
        .authorized{
            border-color: green;
            color: green;
        }
        .failed{
            border-color: red;
            color: red;
        }
    </style>
    <div class="row PaymentPage">
        <div class="col-6">
            <div class="card bg-success">
                <div class="card-body text-white">
                    <h3 class="text-white">€ {$sPaymentValue|number_format:2:",":"."}</h3>

                    <p>Saldo op dit moment</p>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card bg-primary">
                <div class="card-body">
                    {if $sPaymentDate !== Null}
                        <h3 class="text-white">{$sPaymentDate|date_format:"%d-%m-%Y"}</h3>
                    {else}
                        <h3 class="text-white">Geen datum</h3>
                    {/if}

                    <p class="text-white">Datum uitbetaling saldo</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-12">
            <div class="card card-primary paymentsTimeLine">
                <div class="card-header">
                    <h2 class="card-title">Betalingen van de afgelopen 2 weken</h2>
                </div>
                <div class="card-body">
                    <canvas></canvas>
                </div>
                <div class="overlay">
                    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="card card-secondary Issuers">
                <div class="card-header">
                    <h2 class="card-title">Gekozen banken</h2>
                </div>
                <div class="card-body">
                    <canvas></canvas>
                </div>
                <div class="overlay">
                    <i class="fas fa-2x fa-sync-alt fa-spin"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-secondary">
        <div class="card-header">
            <h2 class="card-title">Overzicht</h2>
        </div>
        <div class="card-body table-responsive">
            {assign var="iPaymentsAmount" value="0"}
            <table class="table Arura-Table">
                <thead>
                <tr>
                    <th>Omschrijving</th>
                    <th>Bedrag</th>
                    <th>Tijd</th>
                    <th>Status</th>
                    <th>IBAN</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$aPayments key=$iKey item=aPayment}
                    <tr>
                        <td>{$aPayment.Payment_Description}</td>
                        <td>€ {$aPayment.Payment_Amount|number_format:2:",":"."}</td>
                        {if $aPayment.Payment_Status === "paid"}
                            {$iPaymentsAmount = $iPaymentsAmount + $aPayment.Payment_Amount}
                        {/if}

                        <td data-sort="{$aPayment.Payment_Timestamp}">{$aPayment.Payment_Timestamp|date_format:"%H:%M %d-%m-%Y"}</td>
                        <td data-sort="{$aPayment.Payment_Status}">
                            <div class="payment {$aPayment.Payment_Status}">
                                {$aPayment.Payment_Status}
                            </div>
                        </td>
                        <td>{$aPayment.Payment_Card}</td>
                    </tr>
                {/foreach}
                </tbody>
                <tfoot>
                <tr>
                    <th><strong>Totaal:</strong></th>
                    <th><strong>€ {$iPaymentsAmount|number_format:2:",":"."}</strong></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
{/block}