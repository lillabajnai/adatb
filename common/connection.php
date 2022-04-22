<?php
function csatlakozas() {
    $username = "U0LB4I";
    $password = "oracleHighlyOverrated";
    $tns = "
(DESCRIPTION =
    (ADDRESS_LIST =
      (ADDRESS = (PROTOCOL = TCP)(HOST = localhost)(PORT = 1521))
    )
    (CONNECT_DATA =
      (SID = xe)
    )
  )";

    return oci_connect($username, $password, $tns,'UTF8');
}

function csatlakozas_zarasa($csatl) {
    oci_close($csatl);
}