{extends "../../../index.tpl"}

{block breadcrum}
    <li class="breadcrumb-item"><a href="/dashboard/winkel/evenementen">Evenementen</a></li>
    <li class="breadcrumb-item active">Evenement aanmaken</li>
{/block}

{block content}
    <div class="card card-primary">
        <div class="card-header">
            <h2 class="card-title">Aanmaken</h2>
        </div>
        <div class="card-body">
            {$eventForm->StartForm()}
            <div class="row">
                <div class="col-md-6">
                    {$eventForm->getControl("Event_Name")}
                </div>
                <div class="col-md-6">
                    {$eventForm->getControl("Event_Slug")}
                </div>
                <div class="col-12">
                    {$eventForm->getControl("Event_Location")}
                </div>
                <div class="col-md-6">
                    {$eventForm->getControl("Event_Start_Timestamp")}
                </div>
                <div class="col-md-6">
                    {$eventForm->getControl("Event_End_Timestamp")}
                </div>
                <div class="col-12">
                    {$eventForm->getControl("Event_Description", "richtext")}
                </div>
            </div>
            <hr/>
            <div class="row">
                <div class="col-md-6">
                    {$eventForm->getControl("Event_Banner", "file-selector")}
                </div>
                <div class="col-md-6">
                    {$eventForm->getControl("Event_Registration_End_Timestamp")}
                </div>
                <div class="col-md-6">
                    {$eventForm->getControl("Event_Capacity")}
                </div>
                <div class="col-md-6">
                    {$eventForm->getControl("Event_Organizer_User_Id")}
                </div>
                <div class="col-12">
                    {$eventForm->getControl("Event_IsActive")} <small>Inschrijving mogelijk.</small>
                </div>
                <div class="col-12">
                    {$eventForm->getControl("Event_IsVisible")} <small>Evenement is te zien in overzicht op website</small>
                </div>
                <div class="col-12">
                    {$eventForm->getControl("Event_IsPublic")} <small>Evenement url bereikbaar.</small>
                </div>
            </div>
            {$eventForm->getControl("submit")}
            {$eventForm->EndForm()}

        </div>
    </div>
{/block}