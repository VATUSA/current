<br>
<b>Website URL:</b>
<input type="text" id="facurl" class="form-control" value="{{$facility->url}}"
       autocomplete="off"/>
<button class="btn btn-primary" onClick="updateUrl()">Update</button>
<br><br>
<b>Development Website URL(s):</b>
<p class="help-block">Multiple Dev URLs can be specified, seperated by a
    <strong>comma</strong>.</p>
<input type="text" id="facurldev" class="form-control" value="{{$facility->url_dev}}"
       autocomplete="off"/>
<button class="btn btn-primary" onClick="updateDevUrl()">Update</button>
<hr>
<h1>API (v2)</h1>
<fieldset>
    <legend>Live</legend>
    <b>API JSON Web Key (JWK):</b> (<a href="https://tools.ietf.org/html/rfc7515">RFC7515</a>
    page
    38) --
    symmetric key<br>
    <input class="form-control" type="text" id="textapiv2jwk"
           value="{{$facility->apiv2_jwk}}" readonly autocomplete="off"><br>
    <button class="btn btn-primary" onClick="apiv2JWK()">Generate New</button>
    <br><br>
    <b>API Key:</b><br><input class="form-control" type="text" id="apikey"
                              value="{{$facility->apikey}}" autocomplete="off"><br>
    <button class="btn btn-primary" onClick="apiGen()">Generate New</button>
</fieldset>
<br>
<fieldset>
    <legend>Development</legend>
    <b>Sandbox API JSON Web Key (JWK):</b> (<a
            href="https://tools.ietf.org/html/rfc7515">RFC
        7515</a> page
    38) --
    symmetric key<br>
    <p class="help-block">Development Website URL must be set correctly in order for
        returned data to be formatted according to RFC 7515.</p>
    <input class="form-control" type="text" id="textapiv2jwkdev"
           value="{{$facility->apiv2_jwk_dev}}" readonly autocomplete="off"><br>
    <button class="btn btn-primary" onClick="apiv2JWK(true)">Generate New</button>
    <button class="btn btn-warning" onClick="clearDevAPIv2JWK()">Clear</button>
    <br><br>
    <b>Sandbox API Key:</b><br>
    <p class="help-block">Use this key to prevent the live database from being
        changed.</p>
    <input class="form-control" type="text" id="apisbkey"
           value="{{$facility->api_sandbox_key}}" autocomplete="off"><br>
    <button class="btn btn-primary" onClick="apiSBGen()">Generate New</button>
</fieldset>