
/***************************	Validating Email	***************************/
function emailCheck (emailStr) {
var emailPat=/^(.+)@(.+)$/
var specialChars="\\(\\)<>@,;:\\\\\\\"\\.\\[\\]"
var validChars="\[^\\s" + specialChars + "\]"
var quotedUser="(\"[^\"]*\")"
var ipDomainPat=/^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/
var atom=validChars + '+'
var word="(" + atom + "|" + quotedUser + ")"
var userPat=new RegExp("^" + word + "(\\." + word + ")*$")
var domainPat=new RegExp("^" + atom + "(\\." + atom +")*$")
var matchArray=emailStr.match(emailPat)
if (matchArray==null) {
	alert("Email address seems incorrect (check @ and .'s)")
	return false
}
var user=matchArray[1]
var domain=matchArray[2]
if (user.match(userPat)==null) {
    alert("The username doesn't seem to be valid.")
    return false
}
var IPArray=domain.match(ipDomainPat)
if (IPArray!=null) {
    // this is an IP address
	  for (var i=1;i<=4;i++) {
	    if (IPArray[i]>255) {
	        alert("Destination IP address is invalid!")
		return false
	    }
    }
    return true
}
var domainArray=domain.match(domainPat)
if (domainArray==null) {
	alert("The domain name doesn't seem to be valid.")
    return false
}
var atomPat=new RegExp(atom,"g")
var domArr=domain.match(atomPat)
var len=domArr.length
if (domArr[domArr.length-1].length<2 || 
    domArr[domArr.length-1].length>4) {
   alert("Email address must end in a valid domain, or two letter country.")
   return false
}
if (len<2) {
   var errStr="Email address is missing a hostname!"
   alert(errStr)
   return false
}

   var str=emailStr; 
   if (emailStr.indexOf(" ")>=0)
		{
		alert ("Blank space not allowed inside email!");
		return false;
		}
	
		if (emailStr.indexOf("@",1) == -1)
		{
			alert("Invalid E-Mail address");
			return(false);
		}
		if (emailStr.indexOf("@") == 0)
		{
			alert("Invalid E-Mail address");
			return(false);
		}
		if (emailStr.indexOf(".",5) == -1)
		{
			alert("Invalid E-Mail address");
			return(false);
		}
		if (emailStr.indexOf(".") == 0)
		{
			alert("Invalid E-Mail address");
			return(false);
		}
		
		if ((emailStr.lastIndexOf(".")) -(emailStr.indexOf("@"))<3 )
		{
		
			alert("Invalid E-Mail address");
			return(false);
		}
		
		if ((emailStr.length)-(emailStr.indexOf("."))<2)
		{
			alert("Invalid E-Mail address");
			return(false);
		}

		var posat=str.indexOf("@");
		var posdot=str.indexOf(".");
		var rposdot=str.lastIndexOf(".");
		//alert(posat); 
		//alert(posdot);
		//alert(rposdot);
		
		
		if(rposdot==posdot)
		if((posdot < posat) || (posdot-posat < 3))
		{
		//alert("needs at last 3 cars between @ and . sign");
		alert("Invalid E-Mail address");
		return false;
		}
		
		if(str.charAt(str.length-1)==".")
		{
		//alert("cannot end with .");
		alert("Invalid E-Mail address");
		return false;
		}
		
		if(str.charAt(str.length-1)=="@")
		{
		//alert("cannot end with @");
		alert("Invalid E-Mail address");
		return false;
		}
		
		var j=0;
		for( var i=0;i<str.length;i++)
		{
		if(str.charAt(i) == "@")
		j++;
		}
		if(j > 1)
		{
		//alert("only one @ sign allowed");
		alert("Invalid E-Mail address");
		return false;
		}

return true;
}

function validate_email(email,mandatory,errmsg)
{
	if (mandatory==1 && email.value=='')
	{
		alert(errmsg);
		email.focus();
		return (false);
	}
	if (email.value!='' && !emailCheck (email.value) )
	{
		email.focus();
		email.select();
		return (false);
	}
	return(true);
}

function validate_email_without_msg(email){
	if (email.value==''){		
		return (false);
	}
	if (email.value!='' && !emailCheck (email.value) ){		
		return (false);
	}
	return(true);
}
/***************************	/Validating Email	***************************/


/***************************	Validating Text	***************************/
function validate_text_without_msg(data){
	if (data.value==''){		
		return (false);
	}	
	if (data.value!='' && (data.value.replace(/^\s+|\s+$/, '').length<=0) ){	
		return (false);
	}
	return(true);
}


function validate_text(data,mandatory,errmsg)
{
	if (mandatory==1 && data.value=='')
	{
		alert(errmsg);
		data.focus();
		data.select();
		return (false);
	}
	
	if (data.value!='' && (data.value.replace(/^\s+|\s+$/, '').length<=0) )
	{
		alert(errmsg);
		data.focus();
		data.select();
		return (false);
	}
	return(true);
}
/***************************	/Validating Text	***************************/


/***************************	Validating Text	***************************/
function validate_text_without_alert(data,mandatory){
	if (mandatory==1 && data.value==''){
		return false;
	}
	
	if (data.value!='' && (data.value.replace(/^\s+|\s+$/, '').length<=0) ){
		return false;
	}
	return true;
}
/***************************	/Validating Text	***************************/




/***************************	Validating Numeric	***************************/
function validate_numeric(data,mandatory,errmsg)
{
	if (mandatory==1 && data.value=='')
	{
		alert(errmsg);
		data.focus();
		return (false);
	}
	if (data.value!='' && (isNaN(data.value) || (data.value<0) || (data.value.replace(/^\s+|\s+$/, '').length<=0)) )
	{
		alert(errmsg);
		data.focus();
		data.select();
		return (false);
	}
	return(true);
}



function validate_numeric_without_msg(data){
	if (data.value==''){		
		return (false);
	}
	if (data.value!='' && (isNaN(data.value) || (data.value<0) ||  (parseInt(data.value) == 0) || (data.value.replace(/^\s+|\s+$/, '').length<=0)) ){
		return (false);
	}
	return(true);
}

/***************************	/Validating Numeric	***************************/


/***************************	Validating price	***************************/
function validate_price(data,mandatory,errmsg){
	if (mandatory==1 && data.value==''){
		alert(errmsg);
		data.focus();
		return (false);
	}
	if (data.value!='' && (isNaN(data.value) || (data.value<0) ||  (parseFloat(data.value) == 0) || (data.value.replace(/^\s+|\s+$/, '').length<=0)) ){
		alert(errmsg);
		data.focus();
		data.select();
		return (false);
	}
	return(true);
}
/***************************	/Validating Numeric	***************************/



/***************************	Validating Integer	***************************/
function validate_integer(data,mandatory,errmsg)
{
	if (mandatory==1 && data.value=='')
	{
		alert(errmsg);
		data.focus();
		return (false);
	}
	if (   data.value!='' && (   isNaN(data.value) || (data.value<0) || (data.value.replace(/^\s+|\s+$/, '').length<=0) || (data.value.indexOf('.')!=-1)   )   )
	{
		alert(errmsg);
		data.focus();
		data.select();
		return (false);
	}
	return(true);
}

function validate_integer_without_msg(data){
	if (data.value=='')	{	
		return (false);
	}
	if (data.value!='' && (isNaN(data.value) || (data.value<0) || (data.value.replace(/^\s+|\s+$/, '').length<=0) || (data.value.indexOf('.')!=-1))){
		return (false);
	}
	return(true);
}
/***************************	/Validating Integer	***************************/

/***************************	Check Minimum Length	***************************/
function validate_fixed_length(data,len,errmsg)
{
	
	if (   data.value!='' && (data.value.length != len) )
	{
		alert(errmsg);
		data.focus();
		data.select();
		return (false);
	}
	return(true);
}
/***************************	/Check Minimum Length	***************************/



/***************************	Check Minimum Length	***************************/
function validate_min_length(data,len,errmsg)
{
	if (   data.value!='' && (data.value.length<len) )
	{
		alert(errmsg);
		data.focus();
		data.select();
		return (false);
	}
	return(true);
}
/***************************	/Check Minimum Length	***************************/


/***************************	Check Maximum Length	***************************/
function validate_max_length(data,len,errmsg)
{
	if (   data.value!='' && (data.value.length>len) )
	{
		alert(errmsg);
		data.focus();
		data.select();
		return (false);
	}
	return(true);
}
/***************************	/Check Maximum Length	***************************/



/***************************	Phone number validation	***************************/


// Declaring required variables
var digits = "0123456789";
// non-digit characters which are allowed in phone numbers
var phoneNumberDelimiters = "()- ";
// characters which are allowed in international phone numbers
// (a leading + is OK)
var validWorldPhoneChars = phoneNumberDelimiters + "+";
// Minimum no of digits in an international phone no.
var minDigitsInIPhoneNumber = 10;

function isInteger(s)
{   var i;
    for (i = 0; i < s.length; i++)
    {   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag)
{   var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++)
    {   
        // Check that current character isn't whitespace.
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function checkInternationalPhone(strPhone){
s=stripCharsInBag(strPhone,validWorldPhoneChars);
return (isInteger(s) && s.length >= minDigitsInIPhoneNumber);
}

function validate_phone_no(data,mandatory,errmsg){
	var Phone=data
	
	if (mandatory==1 && data.value=='')
	{
		alert(errmsg);
		data.focus();
		return (false);
	}
	if (   data.value!='' && checkInternationalPhone(Phone.value)==false){
		alert(errmsg);
		Phone.focus();
		Phone.select();
		return false
	}
	return true
 }
/***************************	/Phone number validation	***************************/

/***************************	Credit Card Validation	******************************/
function validateCreditCard(s) {
var v = "0123456789";
var w = "";
for (var i=0; i < s.length; i++) {
x = s.charAt(i);
if (v.indexOf(x,0) != -1)
w += x;
}
var j = w.length / 2;
if (j < 6.5 || j > 8 || j == 7) return false;
var k = Math.floor(j);
var m = Math.ceil(j) - k;
var c = 0;
for (var i=0; i<k; i++) {
a = w.charAt(i*2+m) * 2;
c += a > 9 ? Math.floor(a/10 + a%10) : a;
}
for (var i=0; i<k+m; i++) c += w.charAt(i*2+1-m) * 1;
return (c%10 == 0);
}

function validate_credit_card(data,mandatory,errmsg)
{
	if (mandatory==1 && data.value=='')
	{
		alert(errmsg);
		data.focus();
		return (false);
	}
	
	if (data.value!='' && (validateCreditCard(data.value)==false) )
	{
		alert(errmsg);
		data.focus();
		data.select();
		return (false);
	}
	return(true);
}

/***************************	Credit Card Validation	******************************/





// Declaring valid date character, minimum year and maximum year
var dtCh= "/";
var minYear=1900;
var maxYear=2100;

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   } 
   return this
}

function isDate(dtStr){
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)
	var strMonth=dtStr.substring(0,pos1)
	var strDay=dtStr.substring(pos1+1,pos2)
	var strYear=dtStr.substring(pos2+1)
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (pos1==-1 || pos2==-1){
		alert("The date format should be : mm/dd/yyyy")
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){
		alert("Please enter a valid month")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("Please enter a valid day")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Please enter a valid 4 digit year between "+minYear+" and "+maxYear)
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("Please enter a valid date")
		return false
	}
return true
}





//Manual...............................

// Email Verification
		//return validate_email(document.FormName.FieldName,0);	//2nd arg=if mandatory then 1 else 0
// Text Verification
		//return validate_text(document.FormName.FieldName,0,"Message..");	//2nd arg=if mandatory then 1 else 0
// Min Length Verification
		//return validate_min_length(document.FormName.FieldName,MinLen,"Message..");	//2nd arg=if mandatory then 1 else 0
// Max Length Verification
		//return validate_max_length(document.FormName.FieldName,MaxLen,"Message..");	//2nd arg=if mandatory then 1 else 0
// Numeric Verification
		//return validate_numeric(document.FormName.FieldName,0,"Message..");	//2nd arg=if mandatory then 1 else 0
// Integer Verification
		//return validate_integer(document.FormName.FieldName,0,"Message..");	//2nd arg=if mandatory then 1 else 0
// Phone number Verification
		//return validate_phone_no(document.FormName.FieldName,0,"Message..");	//2nd arg=if mandatory then 1 else 0
// Credit Card Verification
		//return validate_credit_card(document.FormName.FieldName,0,"Message..");	//2nd arg=if mandatory then 1 else 0


function allLetter(uname,errmsg){   
	var letters = /^[A-Za-z]+$/;  
	if(uname.value.match(letters)){  
		return true;  
	}else{  
		alert(errmsg);  
		uname.focus();  
	return false;  
	} 
}  

function allLetterandNumber(uname,errmsg){   
	var letters = /^[a-zA-Z0-9]+$/;  
	if(uname.value.match(letters)){  
		return true;  
	}else{  
		alert(errmsg);  
		uname.focus();  
	return false;  
	} 
}  

function allnumeric(uzip,errmsg){   
	var numbers = /^[0-9]+$/;  
    if(uzip.value.match(numbers)){  
   		return true;  
    }else{  
    	alert(errmsg);  
    	uzip.focus();  
    	return false;  
	}  
}  

function passid_validation(passid,min,max){ //*************  Password 
	var passid_len = passid.value.length;  
	alert(passid_len);
	if (passid_len == 0 ||passid_len >= max || passid_len < min){  
		alert("Password must be of length between "+min+" to "+max);  
		passid.focus();  
		return false;  
	}  
	return true;
} 

function addslashes(str) {
  //  discuss at: http://phpjs.org/functions/addslashes/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Ates Goral (http://magnetiq.com)
  // improved by: marrtins
  // improved by: Nate
  // improved by: Onno Marsman
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Oskar Larsson Högfeldt (http://oskar-lh.name/)
  //    input by: Denny Wardhana
  //   example 1: addslashes("kevin's birthday");
  //   returns 1: "kevin\\'s birthday"

  return (str + '')
    .replace(/[\\"']/g, '\\$&')
    .replace(/\u0000/g, '\\0');
}



