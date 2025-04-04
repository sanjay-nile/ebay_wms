<header class="header">
	<div class="container">
		<div class="header-navigation">
			<nav class="navbar navbar-expand-lg">
				<div class="navbar-brand mobile-logo">
					<img src="{{  asset('public/images/jaded_logo.jpg') }}" height="40">
				</div>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
				</button>
				
				<div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
					<ul class="navbar-nav ml-auto">
						<li class="nav-item"><a href="{{ route('jaded.home') }}"> {{googleTranslate('Create a Return')}}  </a> </li>
						<li class="nav-item"><a href="{{ route('jaded.tracking') }}">{{googleTranslate('Track My Return')}} </a> </li>
						<li class="nav-item"><a href="{{ route('jaded.help') }}">{{googleTranslate('Help')}}</a></li>
						<li class="nav-item">
							<select class="form-select changeLang">
                            <option value="en" {{ session()->get('locale') == 'en' ? 'selected' : '' }}>English</option>                            
							<option value="ar_AE" {{ session()->get('locale') == 'ar_AE' ? 'selected' : '' }}>Arabic (United Arab Emirates)</option>
									<option value="ar" {{ session()->get('locale') == 'ar' ? 
							'selected' : '' }}>Arabic</option>
														<option value="as" {{ session()->get('locale') == 'as' ? 
							'selected' : '' }}>Assamese</option>
														<option value="asa" {{ session()->get('locale') == 'asa' ? 
							'selected' : '' }}>Asu</option>
														<option value="az" {{ session()->get('locale') == 'az' ? 
							'selected' : '' }}>Azerbaijani</option>
														<option value="bas" {{ session()->get('locale') == 'bas' ? 
							'selected' : '' }}>Basaa</option>
														<option value="be" {{ session()->get('locale') == 'be' ? 
							'selected' : '' }}>Belarusian</option>
														<option value="bem" {{ session()->get('locale') == 'bem' ? 
							'selected' : '' }}>Bemba</option>
														<option value="bez" {{ session()->get('locale') == 'bez' ? 
							'selected' : '' }}>Bena</option>
														<option value="bg" {{ session()->get('locale') == 'bg' ? 
							'selected' : '' }}>Bulgarian</option>
														<option value="bm" {{ session()->get('locale') == 'bm' ? 
							'selected' : '' }}>Bambara</option>
														<option value="bn" {{ session()->get('locale') == 'bn' ? 
							'selected' : '' }}>Bengali</option>
														<option value="bo" {{ session()->get('locale') == 'bo' ? 
							'selected' : '' }}>Tibetan</option>
														<option value="br" {{ session()->get('locale') == 'br' ? 
							'selected' : '' }}>Breton</option>
														<option value="bs" {{ session()->get('locale') == 'bs' ? 
							'selected' : '' }}>Bosnian</option>
														<option value="ca" {{ session()->get('locale') == 'ca' ? 
							'selected' : '' }}>Catalan</option>
														<option value="ce" {{ session()->get('locale') == 'ce' ? 
							'selected' : '' }}>Chechen</option>
														<option value="chr" {{ session()->get('locale') == 'chr' ? 
							'selected' : '' }}>Cherokee</option>
														<option value="cs" {{ session()->get('locale') == 'cs' ? 
							'selected' : '' }}>Czech</option>
														<option value="cy" {{ session()->get('locale') == 'cy' ? 
							'selected' : '' }}>Welsh</option>
														<option value="da" {{ session()->get('locale') == 'da' ? 
							'selected' : '' }}>Danish</option>
														<option value="dav" {{ session()->get('locale') == 'dav' ? 
							'selected' : '' }}>Taita</option>
														<option value="de" {{ session()->get('locale') == 'de' ? 
							'selected' : '' }}>German</option>
														<option value="dje" {{ session()->get('locale') == 'dje' ? 
							'selected' : '' }}>Zarma</option>
														<option value="dsb" {{ session()->get('locale') == 'dsb' ? 
							'selected' : '' }}>Lower Sorbian</option>
														<option value="dua" {{ session()->get('locale') == 'dua' ? 
							'selected' : '' }}>Duala</option>
														<option value="dyo" {{ session()->get('locale') == 'dyo' ? 
							'selected' : '' }}>Jola-Fonyi</option>
														<option value="dz" {{ session()->get('locale') == 'dz' ? 
							'selected' : '' }}>Dzongkha</option>
														<option value="ebu" {{ session()->get('locale') == 'ebu' ? 
							'selected' : '' }}>Embu</option>
														<option value="ee" {{ session()->get('locale') == 'ee' ? 
							'selected' : '' }}>Ewe</option>
														<option value="el" {{ session()->get('locale') == 'el' ? 
							'selected' : '' }}>Greek</option>
														<option value="eo" {{ session()->get('locale') == 'eo' ? 
							'selected' : '' }}>Esperanto</option>
														<option value="es" {{ session()->get('locale') == 'es' ? 
							'selected' : '' }}>Spanish</option>
														<option value="et" {{ session()->get('locale') == 'et' ? 
							'selected' : '' }}>Estonian</option>
														<option value="eu" {{ session()->get('locale') == 'eu' ? 
							'selected' : '' }}>Basque</option>
														<option value="ewo" {{ session()->get('locale') == 'ewo' ? 
							'selected' : '' }}>Ewondo</option>
														<option value="fa" {{ session()->get('locale') == 'fa' ? 
							'selected' : '' }}>Persian</option>
														<option value="ff" {{ session()->get('locale') == 'ff' ? 
							'selected' : '' }}>Fulah</option>
														<option value="fi" {{ session()->get('locale') == 'fi' ? 
							'selected' : '' }}>Finnish</option>
														<option value="fil" {{ session()->get('locale') == 'fil' ? 
							'selected' : '' }}>Filipino</option>
														<option value="fo" {{ session()->get('locale') == 'fo' ? 
							'selected' : '' }}>Faroese</option>
														<option value="fr" {{ session()->get('locale') == 'fr' ? 
							'selected' : '' }}>France</option>
														<option value="fur" {{ session()->get('locale') == 'fur' ? 
							'selected' : '' }}>Friulian</option>
														<option value="ga" {{ session()->get('locale') == 'ga' ? 
							'selected' : '' }}>Irish</option>
														<option value="gd" {{ session()->get('locale') == 'gd' ? 
							'selected' : '' }}>Scottish Gaelic</option>
														<option value="gl" {{ session()->get('locale') == 'gl' ? 
							'selected' : '' }}>Galician</option>
														<option value="gu" {{ session()->get('locale') == 'gu' ? 
							'selected' : '' }}>Gujarati</option>
														<option value="guz" {{ session()->get('locale') == 'guz' ? 
							'selected' : '' }}>Gusii</option>
														<option value="gv" {{ session()->get('locale') == 'gv' ? 
							'selected' : '' }}>Manx</option>
														<option value="ha" {{ session()->get('locale') == 'ha' ? 
							'selected' : '' }}>Hausa</option>
														<option value="haw" {{ session()->get('locale') == 'haw' ? 
							'selected' : '' }}>Hawaiian</option>
														<option value="hi" {{ session()->get('locale') == 'hi' ? 
							'selected' : '' }}>Hindi</option>
														<option value="hr" {{ session()->get('locale') == 'hr' ? 
							'selected' : '' }}>Croatian</option>
							<option value="zh" {{ session()->get('locale') == 'zh' ? 
							'selected' : '' }}>Chinese</option>
														<option value="hsb" {{ session()->get('locale') == 'hsb' ? 
							'selected' : '' }}>Upper Sorbian</option>
														<option value="hu" {{ session()->get('locale') == 'hu' ? 
							'selected' : '' }}>Hungarian</option>
														<option value="hy" {{ session()->get('locale') == 'hy' ? 
							'selected' : '' }}>Armenian</option>
														<option value="ig" {{ session()->get('locale') == 'ig' ? 
							'selected' : '' }}>Igbo</option>
														<option value="ii" {{ session()->get('locale') == 'ii' ? 
							'selected' : '' }}>Sichuan Yi</option>
														<option value="in" {{ session()->get('locale') == 'in' ? 
							'selected' : '' }}>Indonesian</option>
														<option value="is" {{ session()->get('locale') == 'is' ? 
							'selected' : '' }}>Icelandic</option>
														<option value="it" {{ session()->get('locale') == 'it' ? 
							'selected' : '' }}>Italian</option>
														<option value="iw" {{ session()->get('locale') == 'iw' ? 
							'selected' : '' }}>Hebrew</option>
														<option value="ja" {{ session()->get('locale') == 'ja' ? 
							'selected' : '' }}>Japanese</option>
														<option value="jgo" {{ session()->get('locale') == 'jgo' ? 
							'selected' : '' }}>Ngomba</option>
														<option value="jmc" {{ session()->get('locale') == 'jmc' ? 
							'selected' : '' }}>Machame</option>
														<option value="ka" {{ session()->get('locale') == 'ka' ? 
							'selected' : '' }}>Georgian</option>
														<option value="kab" {{ session()->get('locale') == 'kab' ? 
							'selected' : '' }}>Kabyle</option>
														<option value="kam" {{ session()->get('locale') == 'kam' ? 
							'selected' : '' }}>Kamba</option>
														<option value="kde" {{ session()->get('locale') == 'kde' ? 
							'selected' : '' }}>Makonde</option>
														<option value="kea" {{ session()->get('locale') == 'kea' ? 
							'selected' : '' }}>Kabuverdianu</option>
														<option value="khq" {{ session()->get('locale') == 'khq' ? 
							'selected' : '' }}>Koyra Chiini</option>
														<option value="ki" {{ session()->get('locale') == 'ki' ? 
							'selected' : '' }}>Kikuyu</option>
														<option value="kk" {{ session()->get('locale') == 'kk' ? 
							'selected' : '' }}>Kazakh</option>
														<option value="kkj" {{ session()->get('locale') == 'kkj' ? 
							'selected' : '' }}>Kako</option>
														<option value="kl" {{ session()->get('locale') == 'kl' ? 
							'selected' : '' }}>Kalaallisut</option>
														<option value="kln" {{ session()->get('locale') == 'kln' ? 
							'selected' : '' }}>Kalenjin</option>
														<option value="km" {{ session()->get('locale') == 'km' ? 
							'selected' : '' }}>Khmer</option>
														<option value="kn" {{ session()->get('locale') == 'kn' ? 
							'selected' : '' }}>Kannada</option>
														<option value="ko" {{ session()->get('locale') == 'ko' ? 
							'selected' : '' }}>Korean</option>
														<option value="kok" {{ session()->get('locale') == 'kok' ? 
							'selected' : '' }}>Konkani</option>
														<option value="ks" {{ session()->get('locale') == 'ks' ? 
							'selected' : '' }}>Kashmiri</option>
														<option value="ksb" {{ session()->get('locale') == 'ksb' ? 
							'selected' : '' }}>Shambala</option>
														<option value="ksf" {{ session()->get('locale') == 'ksf' ? 
							'selected' : '' }}>Bafia</option>
														<option value="ksh" {{ session()->get('locale') == 'ksh' ? 
							'selected' : '' }}>Colognian</option>
														<option value="kw" {{ session()->get('locale') == 'kw' ? 
							'selected' : '' }}>Cornish</option>
														<option value="ky" {{ session()->get('locale') == 'ky' ? 
							'selected' : '' }}>Kyrgyz</option>
														<option value="lag" {{ session()->get('locale') == 'lag' ? 
							'selected' : '' }}>Langi</option>
														<option value="lb" {{ session()->get('locale') == 'lb' ? 
							'selected' : '' }}>Luxembourgish</option>
														<option value="lg" {{ session()->get('locale') == 'lg' ? 
							'selected' : '' }}>Ganda</option>
														<option value="lt" {{ session()->get('locale') == 'lt' ? 
							'selected' : '' }}>Lithuanian</option>
														<option value="lv" {{ session()->get('locale') == 'lv' ? 
							'selected' : '' }}>Latvian</option>
														<option value="mk" {{ session()->get('locale') == 'mk' ? 
							'selected' : '' }}>Macedonian</option>
														<option value="ms" {{ session()->get('locale') == 'ms' ? 
							'selected' : '' }}>Malay</option>
														<option value="mt" {{ session()->get('locale') == 'mt' ? 
							'selected' : '' }}>Maltese</option>
														<option value="nl" {{ session()->get('locale') == 'nl' ? 
							'selected' : '' }}>Dutch</option>
														<option value="no" {{ session()->get('locale') == 'no' ? 
							'selected' : '' }}>Norwegian</option>
														<option value="pl" {{ session()->get('locale') == 'pl' ? 
							'selected' : '' }}>Polish</option>
														<option value="pt" {{ session()->get('locale') == 'pt' ? 
							'selected' : '' }}>Portuguese</option>
														<option value="ro" {{ session()->get('locale') == 'ro' ? 
							'selected' : '' }}>Romanian</option>
														<option value="ru" {{ session()->get('locale') == 'ru' ? 
							'selected' : '' }}>Russian</option>
														<option value="sk" {{ session()->get('locale') == 'sk' ? 
							'selected' : '' }}>Slovak</option>
														<option value="sl" {{ session()->get('locale') == 'sl' ? 
							'selected' : '' }}>Slovenian</option>
														<option value="sq" {{ session()->get('locale') == 'sq' ? 
							'selected' : '' }}>Albanian</option>
														<option value="sr" {{ session()->get('locale') == 'sr' ? 
							'selected' : '' }}>Serbian</option>
														<option value="sv" {{ session()->get('locale') == 'sv' ? 
							'selected' : '' }}>Swedish</option>
														<option value="th" {{ session()->get('locale') == 'th' ? 
							'selected' : '' }}>Thai</option>
														<option value="tr" {{ session()->get('locale') == 'tr' ? 
							'selected' : '' }}>Turkish</option>
														<option value="uk" {{ session()->get('locale') == 'uk' ? 
							'selected' : '' }}>Ukrainian</option>
														<option value="vi" {{ session()->get('locale') == 'vi' ? 
							'selected' : '' }}>Vietnamese</option>
														
														<option value="zu" {{ session()->get('locale') == 'zu' ? 
							'selected' : '' }}>Zulu</option>
							</select>
						</li>
					</ul>
				</div>
			</nav>  
		</div>
	</div>
</header>

<script type="text/javascript">
        var url = "{{ route('changeLang') }}";
        $(".changeLang").change(function(){
            window.location.href = url + "?lang="+ $(this).val();
        });
    </script>