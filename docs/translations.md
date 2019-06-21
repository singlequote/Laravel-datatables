# Translations

[Go back to datatables](https://singlequote.github.io/Laravel-datatables/)
-------------------------------------------------------------------


Translating the datatable is easy, create a new file in your language folder `resources/lang/en` with the name `datatables.php` and paste or translate the following :

[Here you can find the full list of translations](https://datatables.net/plug-ins/i18n/)
 
 ```php
 <?php
return [
    "filter" => "Filter",
    "sEmptyTable" => "No data available in table",
    "sInfo" => "Showing _START_ to _END_ of _TOTAL_ entries",
    "sInfoEmpty" => "Showing 0 to 0 of 0 entries",
    "sInfoFiltered" => "(filtered from _MAX_ total entries)",
    "sInfoPostFix" => "",
    "sInfoThousands" => ",",
    "sLengthMenu" => "Show _MENU_ entries",
    "sLoadingRecords" => "Loading...",
    "sProcessing" => "Processing...",
    "sSearch" => "Search:",
    "sZeroRecords" => "No matching records found",
    "oPaginate" => [
        "sFirst" => "First",
        "sLast" => "Last",
        "sNext" => "Next",
        "sPrevious" => "Previous"
    ],
    "oAria" => [
        "sSortAscending" => " => activate to sort column ascending",
        "sSortDescending" => " => activate to sort column descending"
    ]
];
 ```
 
