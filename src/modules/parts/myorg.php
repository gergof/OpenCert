<?php

$sql=$db->prepare("SELECT COUNT(om.id) AS count, om.role AS role, o.id, o.name, o.country, o.region, o.city, o.address, o.phone, o.email, o.bio, o.rsakey, o.reputation FROM organization_members AS om INNER JOIN organizations AS o ON (o.id=om.org) WHERE om.user=:uid");
$sql->execute(array(":uid"=>$_SESSION["id"]));
$org=$sql->fetch(PDO::FETCH_ASSOC);

if(isset($_GET["myorg"])){
    if($org["count"]!=1){
        \LightFrame\Utils\setError(113);
        die("error");
    }

    if($org["role"]!=2){
        \LightFrame\Utils\setError(114);
        die("error");
    }

    echo json_encode($org);
    die();
}

if(isset($_GET["name_available"])){
    $sql=$db->prepare("SELECT COUNT(id) AS count FROM organizations WHERE name=:name");
    $sql->execute(array(":name"=>$_GET["name_available"]));
    $res=$sql->fetch(PDO::FETCH_ASSOC);

    if($res["count"]==0){
        die("ok");
    }
    else{
        die("error");
    }
}

if(isset($_POST["neworg"])){
    $data=json_decode($_POST["neworg"], true);

    if(!(isset($data["name"]) && isset($data["country"]) && isset($data["region"]) && isset($data["city"]) && isset($data["address"]) && isset($data["phone"]) && isset($data["email"]) && isset($data["bio"]) && isset($data["rsakey"]))){
        \LightFrame\Utils\setError(207);
        die("error");
    }

    if($org["count"]!=0){
        \LightFrame\Utils\setError(115);
        die("error");
    }

    $sql=$db->prepare("SELECT COUNT(id) AS count FROM organizations WHERE name=:name");
    $sql->execute(array(":name"=>$data["name"]));
    $res=$sql->fetch(PDO::FETCH_ASSOC);

    if($res["count"]!=0){
        \LightFrame\Utils\setError(112);
        die("error");
    }

    $sql=$db->prepare("INSERT INTO organizations (name, country, region, city, address, phone, email, bio, rsakey) VALUES (:name, :country, :region, :city, :address, :phone, :email, :bio, :rsakey)");
    $sql->execute(array(":name"=>$data["name"], ":country"=>$data["country"], ":region"=>$data["region"], ":city"=>$data["city"], ":address"=>$data["address"], ":phone"=>$data["phone"], ":email"=>$data["email"], ":bio"=>$data["bio"], ":rsakey"=>$data["rsakey"]));
    $res=$sql->rowCount();
    $newOrgId=$db->lastInsertId();

    if($res<1){
        \LightFrame\Utils\setError(500);
        die("error");
    }
    else{
        $sql=$db->prepare("INSERT INTO organization_members (user, org, role) VALUES (:user, :org, 2)");
        $sql->execute(array(":user"=>$_SESSION["id"], ":org"=>$newOrgId));
        $res=$sql->rowCount();

        if($res<1){
            \LightFrame\Utils\setError(500);
            //roll back org creation
            $sql=$db->prepare("DELETE FROM organizations WHERE id=:id");
            $sql->execute(array(":id"=>$newOrgId));

            die("error");
        }
        else{
            \LightFrame\Utils\setMessage(22);
            die("ok");
        }
    }
}

if(isset($_POST["edit"])){
    if($org["count"]==0){
        \LightFrame\Utils\setError(113);
        die("error");
    }

    if($org["role"]!=2){
        \LightFrame\Utils\setError(114);
        die("restricted");
    }

    $data=json_decode($_POST["edit"], true);

    if(!(isset($data["name"]) && isset($data["region"]) && isset($data["city"]) && isset($data["address"]) && isset($data["phone"]) && isset($data["email"]) && isset($data["bio"]))){
        \LightFrame\Utils\setError(207);
        die("error");
    }

    $sql=$db->prepare("UPDATE organizations SET name=:name, region=:region, city=:city, address=:address, phone=:phone, email=:email, bio=:bio WHERE id=:id");
    $sql->execute(array(":name"=>$data["name"], ":region"=>$data["region"], ":city"=>$data["city"], ":address"=>$data["address"], ":phone"=>$data["phone"], ":email"=>$data["email"], ":bio"=>$data["bio"], ":id"=>$org["id"]));
    $res=$sql->rowCount();

    if($res<1){
        \LightFrame\Utils\setError(500);
        die("error");
    }
    else{
        \LightFrame\Utils\setMessage(23);
        die("ok");
    }
}

if(isset($_POST["leave"])){
    if($org["count"]==0){
        \LightFrame\Utils\setError(113);
        die("error");
    }

    if($org["role"]==2){
        \LightFrame\Utils\setError(116);
        die("error");
    }

    $sql=$db->prepare("DELETE FROM organization_members WHERE user=:user and org=:org");
    $sql->execute(array(":user"=>$_SESSION["id"], ":org"=>$org["id"]));
    $res=$sql->rowCount();

    if($res["count"]<1){
        \LightFrame\Utils\setError(500);
        die("error");
    }
    else{
        \LightFrame\Utils\setMessage(24);
        die("ok");
    }
}

?>

<span style="display: none" id="lang_rsaPrivate"><?php echo $lang["rsa_private"] ?></span>
<span style="display: none" id="lang_rsaPrivateTooltip"><?php echo $lang["rsa_private_tooltip"] ?></span>
<span style="display: none" id="lang_rsaPrivateWrotedown"><?php echo $lang["rsa_private_wrotedown"] ?></span>
<span style="display: none" id="lang_edit"><?php echo $lang["edit"] ?></span>
<span style="display: none" id="lang_name"><?php echo $lang["name"] ?></span>
<span style="display: none" id="lang_region"><?php echo $lang["region"] ?></span>
<span style="display: none" id="lang_city"><?php echo $lang["city"] ?></span>
<span style="display: none" id="lang_address"><?php echo $lang["address"] ?></span>
<span style="display: none" id="lang_phone"><?php echo $lang["phone"] ?></span>
<span style="display: none" id="lang_email"><?php echo $lang["email"] ?></span>
<span style="display: none" id="lang_bio"><?php echo $lang["bio"] ?></span>
<span style="display: none" id="lang_save"><?php echo $lang["save"] ?></span>
<span style="display: none" id="lang_cancel"><?php echo $lang["cancel"] ?></span>
<span style="display: none" id="lang_leaveorg"><?php echo $lang["leaveorg"] ?></span>
<span style="display: none" id="lang_leaveSure"><?php echo $lang["leave_sure"] ?></span>
<?php if($org["count"]==0): ?>
    <!-- the user isn't member of any organization -->
    <form method="POST" action="" onsubmit="ui.myorg.newOrg(event)" id="neworg_form">
        <fieldset class="form">
            <legend class="form__legend">
                <span><?php echo $lang["create_new_org"] ?></span>
            </legend>
            <div class="form__fields">
                <p><?php echo $lang["name"].":" ?></p>
                <div class="checkinput">
                    <input type="text" class="checkinput__input" name="name" placeholder="<?php echo $lang["name"]."..." ?>" required oninput="ui.myorg.validateName(this)"/>
                </div>
                <p><?php echo $lang["country"].":" ?></p>
                <select name="country" required>
                    <option value="AF">Afghanistan</option>
                    <option value="AX">Åland Islands</option>
                    <option value="AL">Albania</option>
                    <option value="DZ">Algeria</option>
                    <option value="AS">American Samoa</option>
                    <option value="AD">Andorra</option>
                    <option value="AO">Angola</option>
                    <option value="AI">Anguilla</option>
                    <option value="AQ">Antarctica</option>
                    <option value="AG">Antigua and Barbuda</option>
                    <option value="AR">Argentina</option>
                    <option value="AM">Armenia</option>
                    <option value="AW">Aruba</option>
                    <option value="AU">Australia</option>
                    <option value="AT">Austria</option>
                    <option value="AZ">Azerbaijan</option>
                    <option value="BS">Bahamas</option>
                    <option value="BH">Bahrain</option>
                    <option value="BD">Bangladesh</option>
                    <option value="BB">Barbados</option>
                    <option value="BY">Belarus</option>
                    <option value="BE">Belgium</option>
                    <option value="BZ">Belize</option>
                    <option value="BJ">Benin</option>
                    <option value="BM">Bermuda</option>
                    <option value="BT">Bhutan</option>
                    <option value="BO">Bolivia, Plurinational State of</option>
                    <option value="BQ">Bonaire, Sint Eustatius and Saba</option>
                    <option value="BA">Bosnia and Herzegovina</option>
                    <option value="BW">Botswana</option>
                    <option value="BV">Bouvet Island</option>
                    <option value="BR">Brazil</option>
                    <option value="IO">British Indian Ocean Territory</option>
                    <option value="BN">Brunei Darussalam</option>
                    <option value="BG">Bulgaria</option>
                    <option value="BF">Burkina Faso</option>
                    <option value="BI">Burundi</option>
                    <option value="KH">Cambodia</option>
                    <option value="CM">Cameroon</option>
                    <option value="CA">Canada</option>
                    <option value="CV">Cape Verde</option>
                    <option value="KY">Cayman Islands</option>
                    <option value="CF">Central African Republic</option>
                    <option value="TD">Chad</option>
                    <option value="CL">Chile</option>
                    <option value="CN">China</option>
                    <option value="CX">Christmas Island</option>
                    <option value="CC">Cocos (Keeling) Islands</option>
                    <option value="CO">Colombia</option>
                    <option value="KM">Comoros</option>
                    <option value="CG">Congo</option>
                    <option value="CD">Congo, the Democratic Republic of the</option>
                    <option value="CK">Cook Islands</option>
                    <option value="CR">Costa Rica</option>
                    <option value="CI">Côte d'Ivoire</option>
                    <option value="HR">Croatia</option>
                    <option value="CU">Cuba</option>
                    <option value="CW">Curaçao</option>
                    <option value="CY">Cyprus</option>
                    <option value="CZ">Czech Republic</option>
                    <option value="DK">Denmark</option>
                    <option value="DJ">Djibouti</option>
                    <option value="DM">Dominica</option>
                    <option value="DO">Dominican Republic</option>
                    <option value="EC">Ecuador</option>
                    <option value="EG">Egypt</option>
                    <option value="SV">El Salvador</option>
                    <option value="GQ">Equatorial Guinea</option>
                    <option value="ER">Eritrea</option>
                    <option value="EE">Estonia</option>
                    <option value="ET">Ethiopia</option>
                    <option value="FK">Falkland Islands (Malvinas)</option>
                    <option value="FO">Faroe Islands</option>
                    <option value="FJ">Fiji</option>
                    <option value="FI">Finland</option>
                    <option value="FR">France</option>
                    <option value="GF">French Guiana</option>
                    <option value="PF">French Polynesia</option>
                    <option value="TF">French Southern Territories</option>
                    <option value="GA">Gabon</option>
                    <option value="GM">Gambia</option>
                    <option value="GE">Georgia</option>
                    <option value="DE">Germany</option>
                    <option value="GH">Ghana</option>
                    <option value="GI">Gibraltar</option>
                    <option value="GR">Greece</option>
                    <option value="GL">Greenland</option>
                    <option value="GD">Grenada</option>
                    <option value="GP">Guadeloupe</option>
                    <option value="GU">Guam</option>
                    <option value="GT">Guatemala</option>
                    <option value="GG">Guernsey</option>
                    <option value="GN">Guinea</option>
                    <option value="GW">Guinea-Bissau</option>
                    <option value="GY">Guyana</option>
                    <option value="HT">Haiti</option>
                    <option value="HM">Heard Island and McDonald Islands</option>
                    <option value="VA">Holy See (Vatican City State)</option>
                    <option value="HN">Honduras</option>
                    <option value="HK">Hong Kong</option>
                    <option value="HU">Hungary</option>
                    <option value="IS">Iceland</option>
                    <option value="IN">India</option>
                    <option value="ID">Indonesia</option>
                    <option value="IR">Iran, Islamic Republic of</option>
                    <option value="IQ">Iraq</option>
                    <option value="IE">Ireland</option>
                    <option value="IM">Isle of Man</option>
                    <option value="IL">Israel</option>
                    <option value="IT">Italy</option>
                    <option value="JM">Jamaica</option>
                    <option value="JP">Japan</option>
                    <option value="JE">Jersey</option>
                    <option value="JO">Jordan</option>
                    <option value="KZ">Kazakhstan</option>
                    <option value="KE">Kenya</option>
                    <option value="KI">Kiribati</option>
                    <option value="KP">Korea, Democratic People's Republic of</option>
                    <option value="KR">Korea, Republic of</option>
                    <option value="KW">Kuwait</option>
                    <option value="KG">Kyrgyzstan</option>
                    <option value="LA">Lao People's Democratic Republic</option>
                    <option value="LV">Latvia</option>
                    <option value="LB">Lebanon</option>
                    <option value="LS">Lesotho</option>
                    <option value="LR">Liberia</option>
                    <option value="LY">Libya</option>
                    <option value="LI">Liechtenstein</option>
                    <option value="LT">Lithuania</option>
                    <option value="LU">Luxembourg</option>
                    <option value="MO">Macao</option>
                    <option value="MK">Macedonia, the former Yugoslav Republic of</option>
                    <option value="MG">Madagascar</option>
                    <option value="MW">Malawi</option>
                    <option value="MY">Malaysia</option>
                    <option value="MV">Maldives</option>
                    <option value="ML">Mali</option>
                    <option value="MT">Malta</option>
                    <option value="MH">Marshall Islands</option>
                    <option value="MQ">Martinique</option>
                    <option value="MR">Mauritania</option>
                    <option value="MU">Mauritius</option>
                    <option value="YT">Mayotte</option>
                    <option value="MX">Mexico</option>
                    <option value="FM">Micronesia, Federated States of</option>
                    <option value="MD">Moldova, Republic of</option>
                    <option value="MC">Monaco</option>
                    <option value="MN">Mongolia</option>
                    <option value="ME">Montenegro</option>
                    <option value="MS">Montserrat</option>
                    <option value="MA">Morocco</option>
                    <option value="MZ">Mozambique</option>
                    <option value="MM">Myanmar</option>
                    <option value="NA">Namibia</option>
                    <option value="NR">Nauru</option>
                    <option value="NP">Nepal</option>
                    <option value="NL">Netherlands</option>
                    <option value="NC">New Caledonia</option>
                    <option value="NZ">New Zealand</option>
                    <option value="NI">Nicaragua</option>
                    <option value="NE">Niger</option>
                    <option value="NG">Nigeria</option>
                    <option value="NU">Niue</option>
                    <option value="NF">Norfolk Island</option>
                    <option value="MP">Northern Mariana Islands</option>
                    <option value="NO">Norway</option>
                    <option value="OM">Oman</option>
                    <option value="PK">Pakistan</option>
                    <option value="PW">Palau</option>
                    <option value="PS">Palestinian Territory, Occupied</option>
                    <option value="PA">Panama</option>
                    <option value="PG">Papua New Guinea</option>
                    <option value="PY">Paraguay</option>
                    <option value="PE">Peru</option>
                    <option value="PH">Philippines</option>
                    <option value="PN">Pitcairn</option>
                    <option value="PL">Poland</option>
                    <option value="PT">Portugal</option>
                    <option value="PR">Puerto Rico</option>
                    <option value="QA">Qatar</option>
                    <option value="RE">Réunion</option>
                    <option value="RO">Romania</option>
                    <option value="RU">Russian Federation</option>
                    <option value="RW">Rwanda</option>
                    <option value="BL">Saint Barthélemy</option>
                    <option value="SH">Saint Helena, Ascension and Tristan da Cunha</option>
                    <option value="KN">Saint Kitts and Nevis</option>
                    <option value="LC">Saint Lucia</option>
                    <option value="MF">Saint Martin (French part)</option>
                    <option value="PM">Saint Pierre and Miquelon</option>
                    <option value="VC">Saint Vincent and the Grenadines</option>
                    <option value="WS">Samoa</option>
                    <option value="SM">San Marino</option>
                    <option value="ST">Sao Tome and Principe</option>
                    <option value="SA">Saudi Arabia</option>
                    <option value="SN">Senegal</option>
                    <option value="RS">Serbia</option>
                    <option value="SC">Seychelles</option>
                    <option value="SL">Sierra Leone</option>
                    <option value="SG">Singapore</option>
                    <option value="SX">Sint Maarten (Dutch part)</option>
                    <option value="SK">Slovakia</option>
                    <option value="SI">Slovenia</option>
                    <option value="SB">Solomon Islands</option>
                    <option value="SO">Somalia</option>
                    <option value="ZA">South Africa</option>
                    <option value="GS">South Georgia and the South Sandwich Islands</option>
                    <option value="SS">South Sudan</option>
                    <option value="ES">Spain</option>
                    <option value="LK">Sri Lanka</option>
                    <option value="SD">Sudan</option>
                    <option value="SR">Suriname</option>
                    <option value="SJ">Svalbard and Jan Mayen</option>
                    <option value="SZ">Swaziland</option>
                    <option value="SE">Sweden</option>
                    <option value="CH">Switzerland</option>
                    <option value="SY">Syrian Arab Republic</option>
                    <option value="TW">Taiwan, Province of China</option>
                    <option value="TJ">Tajikistan</option>
                    <option value="TZ">Tanzania, United Republic of</option>
                    <option value="TH">Thailand</option>
                    <option value="TL">Timor-Leste</option>
                    <option value="TG">Togo</option>
                    <option value="TK">Tokelau</option>
                    <option value="TO">Tonga</option>
                    <option value="TT">Trinidad and Tobago</option>
                    <option value="TN">Tunisia</option>
                    <option value="TR">Turkey</option>
                    <option value="TM">Turkmenistan</option>
                    <option value="TC">Turks and Caicos Islands</option>
                    <option value="TV">Tuvalu</option>
                    <option value="UG">Uganda</option>
                    <option value="UA">Ukraine</option>
                    <option value="AE">United Arab Emirates</option>
                    <option value="GB">United Kingdom</option>
                    <option value="US">United States</option>
                    <option value="UM">United States Minor Outlying Islands</option>
                    <option value="UY">Uruguay</option>
                    <option value="UZ">Uzbekistan</option>
                    <option value="VU">Vanuatu</option>
                    <option value="VE">Venezuela, Bolivarian Republic of</option>
                    <option value="VN">Viet Nam</option>
                    <option value="VG">Virgin Islands, British</option>
                    <option value="VI">Virgin Islands, U.S.</option>
                    <option value="WF">Wallis and Futuna</option>
                    <option value="EH">Western Sahara</option>
                    <option value="YE">Yemen</option>
                    <option value="ZM">Zambia</option>
                    <option value="ZW">Zimbabwe</option>
                </select>
                <p><?php echo $lang["region"].":" ?></p>
                <input type="text" name="region" placeholder="<?php echo $lang["region"]."..." ?>" required/>
                <p><?php echo $lang["city"].":" ?></p>
                <input type="text" name="city" placeholder="<?php echo $lang["city"]."..." ?>" required/>
                <p><?php echo $lang["address"].":" ?></p>
                <input type="text" name="address" placeholder="<?php echo $lang["address"]."..." ?>" required/>
                <p><?php echo $lang["phone"].":" ?></p>
                <input type="text" name="phone" placeholder="<?php echo $lang["phone"]."..." ?>" pattern="^00\d{2}-[0-9]+$" title="<?php echo $lang["phone_tooltip"] ?>" required/>
                <p><?php echo $lang["email"].":" ?></p>
                <input type="email" name="email" placeholder="<?php echo $lang["email"]."..." ?>" required/>
                <p><?php echo $lang["bio"].":" ?></p>
                <textarea name="bio" placeholder="<?php echo $lang["bio_tooltip"]."..." ?>" required></textarea>
                <p><?php echo $lang["rsakey"].":" ?></p>
                <div>
                    <span><?php echo $lang["rsakey_tooltip"] ?></span>
                    <br/>
                    <span><?php echo $lang["rsa_public"].":" ?></span>
                    <br/>
                    <div class="checkinput">
                        <textarea name="rsakey" style="width: 100%" id="rsa_public" placeholder="-----BEGIN RSA PUBLIC KEY-----&#x0a;&#x0a;...&#x0a;&#x0a;-----END RSA PUBLIC KEY-----" rows="5" oninput="ui.myorg.validatePublicKey(this)" required></textarea>
                    </div>
                    <br/>
                    <button type="button" class="button" onclick="ui.myorg.generateKeypair(this)"><?php echo $lang["rsa_generate"] ?></button>
                </div>
            </div>
            <button class="button"><?php echo $lang["ok"] ?></button>
        </fieldset>
    </form>
<?php else: ?>
    <h2><?php echo $org["name"] ?></h2>
    <div style="text-align: center">
        <?php if($org["role"]==2): ?><button type="button" class="button" onclick="ui.main.route('myorg/members')"><?php echo $lang["members"] ?></button><?php endif ?>
        <?php if($org["role"]>=1): ?><button type="button" class="button" onclick="ui.main.route('myorg/examinations')"><?php echo $lang["examinations"] ?></button><?php endif ?>
        <?php if($org["role"]>=1): ?><button type="button" class="button" onclick="ui.main.route('myorg/results')"><?php echo $lang["results"] ?></button><?php endif ?>
        <?php if($org["role"]==2): ?><button type="button" class="button" onclick="ui.main.route('myorg/certificates')"><?php echo $lang["certificates"] ?></button><?php endif ?>
        <button type="button" class="button" onclick="ui.main.route('myorg/statistics')"><?php echo $lang["statistics"] ?></button>
        <button type="button" class="button" onclick="ui.main.route('myorg/doexamination')"><?php echo $lang["doexamination"] ?></button>
        <br/>
        <br style="line-height: 5em"/>
    </div>
    <div>
        <?php if($org["role"]==2): ?><button type="button" class="button" onclick="ui.myorg.editOrg()"><?php echo $lang["edit"] ?></button><?php endif ?>
        <button type="button" class="button button__red" onclick="ui.myorg.leave()"><i class="fa fa-exclamation-triangle"></i> <?php echo $lang["leaveorg"] ?></button>
    </div>
<?php endif ?>