<?php

//*****************************  collect all API URL ********************************//
$edEP_url="https://www.edebit.com.au/IS/edEP.ashx?edNo=200704";
$ch = curl_init($edEP_url);

//*************************** insert/update user info******************************************//
//$edPI_url="https://www.edebit.com.au/IS/edPI.ashx?edNo=200704&clNo=13122006&cl1stName=Mrithjunjoy&cl2ndName=Majumder&clAddr=1+The+Street&clCity=Melbourne&clState=VIC&clPCode=3012";
//$edPI_url .="&clTel=&clEmail=mrithjunjoy@gmail.com&clDlName=Mrithjunjoy+Majumder&clDlNo=&clDlState=&accountType=DD&clMktNo=";
//$ch = curl_init($edPI_url);


//********************************* obtain the password **************************************************************//
//$edPW_url="https://www.edebit.com.au/IS/edPW.ashx?edno=200704";
//$ch = curl_init($edPW_url);
//cd_community=edebit&cd_supplier_business=edebit

//********************obtain an encrypted key to instruct the eDebit system to pre-register *******************//
//$edKI_url="https://www.edebit.com.au/IS/edKI.ashx?cd_crn=200704-13122006";
//$ch = curl_init($edKI_url);
//token=ed2fmpikk0p3mk45uudih03z

//******************************** used to set environment for preregistration of an account – bank or card *****//

//$edReg_url="https://www.edebit.com.au/IS/edReg.ashx?cd_crn=200704-13122006&accountType=DD&token=ed2fmpikk0p3mk45uudih03z&cd_community=edebit&cd_supplier_business=edebit";
//$ch = curl_init($edReg_url);

//******************************** edPage *****//
//$edPage_url="https://www.edebit.com.au/IS/DDInfo.aspx?cd_crn=200704-13122006";
//$ch = curl_init($edPage_url);


curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
$data=curl_exec($ch); 
curl_close($ch);

print $data;

?>