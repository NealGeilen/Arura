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
<div class="row">
    <div class="col-md-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>€ {$sPaymentValue|number_format:2:",":"."}</h3>

                <p>Saldo op dit moment</p>
            </div>
            <div class="icon">
                <i class="fas fa-euro-sign"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                {if $sPaymentDate !== Null}
                    <h3>{$sPaymentDate|date_format:"%d-%m-%Y"}</h3>
                {else}
                    <h3>Geen datum</h3>
                {/if}

                <p>Datum uitbetaling saldo</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-invoice"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-12">
        <div class="card card-primary card-tabs">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="pill" href="#payment-chart-line" role="tab" aria-selected="false">Aantal betalingen</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#payment-banks" role="tab" aria-selected="false">Gebruikten banken</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#payment-averga-time" role="tab" aria-selected="false">Betaal tijden</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade active show" id="payment-chart-line" role="tabpanel">
                        {$sLineChart}
                    </div>
                    <div class="tab-pane fade" id="payment-banks" role="tabpanel">
                        {$sBanksChart}
                    </div>
                    <div class="tab-pane fade" id="payment-averga-time" role="tabpanel">
                        {$sAveragePaymentTime}
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>

<div class="card">
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

                <td data-sort="{$aPayment.Payment_Timestamp}">{$aPayment.Payment_Timestamp|date_format:"%H:%M %d-%m-%y"}</td>
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
                    <td><strong>Totaal:</strong></td>
                    <td><strong>€ {$iPaymentsAmount|number_format:2:",":"."}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

