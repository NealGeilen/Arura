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
<div class="card card-primary card-outline card-tabs">
    <div class="card-header p-0 pt-1 border-bottom-0">
        <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
            {foreach from=$aCharts item=chart}
                <li class="nav-item">
                    <a class="nav-link" data-toggle="pill" href="#payment-{$chart.year}-{$chart.quarter}" role="tab" aria-controls="custom-tabs-two-home" aria-selected="false">{$chart.year} - {$chart.quarter} kwartaal</a>
                </li>
            {/foreach}

        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="custom-tabs-two-tabContent">
            {foreach from=$aCharts item=chart}
                <div class="tab-pane fade" id="payment-{$chart.year}-{$chart.quarter}" role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
                    {$chart.chart}
                </div>
            {/foreach}

        </div>
    </div>
    <!-- /.card -->
</div>
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Allen betalingen</h2>
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

                <td>{$aPayment.Payment_Timestamp|date_format:"%H:%M %d-%m-%y"}</td>
                <td>
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

