<?php

use yii\helpers\Url;
$module = Yii::$app->getModule('cap');

?>
<script type="text/javascript">
<?php $this->beginBlock('JS_END') ?>
		var accounts = <?= json_encode($model->accounts()) ?>;
		var journals = <?= \yii\helpers\Json::encode($model->isNewRecord?$model->id:$model->journals) ?>;						
		var capprec = <?= $module->currency["precision"] ?>;
		
		(function($){
			$.fn.extend({detachOptions: function(o) {
				var s = this;
				return s.each(function(){
					var d = s.data('selectOptions') || [];
					s.find(o).each(function() {
						d.push($(this).detach());
					});
					s.data('selectOptions', d);
				});
			}, attachOptions: function(o) {
				var s = this;
				return s.each(function(){
					var d = s.data('selectOptions') || [];					
					for (var i in d) {						
						s.append(d[i]);						
					}
				});
			}});   

		})(jQuery);

		
		function filterOptions(tipe,increaseon)
		{											
			
			var cek = function(a){									
					/*return ( a["increaseon"] == (increaseon == "debet"?0:1)
							?true:false);
					*/
					return true;		
				};
			
			if (tipe == 1)
			{				
				var cek = function(a){
					return ( (increaseon == "debet"? a["increaseon"] == 0 && a["isbalance"] && a["exchangable"] : false) ||					
							(increaseon != "debet"? a["increaseon"] == 0 && a["isbalance"] && a["exchangable"] : false)
							?true:false);																			
				};		
			}
			else if (tipe == 2)
			{				
				var cek = function(a){
					return ( (increaseon == "debet"? a["increaseon"] == 0 && a["isbalance"] && a["exchangable"] : false) ||					
							(increaseon != "debet"? a["increaseon"] == 1 : false)
							?true:false);																			
				};		
			}
			else if (tipe == 3)
			{
				var cek = function(a){									
					return ( (increaseon == "debet"? a["increaseon"] == 0 && a["isbalance"] && a["exchangable"] : false) ||					
							(increaseon != "debet"? 
								(a["increaseon"] == 0 && a["isbalance"] && !a["exchangable"]) || 
								(a["increaseon"] == 1) 
							: false)
							?true:false);																			
				};
			}							
			else if (tipe == 4)
			{
				var cek = function(a){									
					return ( (increaseon == "debet"? a["increaseon"] == 0 && a["isbalance"] && !a["exchangable"] : false) ||					
							(increaseon != "debet"? 
									(a["increaseon"] == 0 && a["isbalance"] && a["exchangable"] ) ||
									(a["increaseon"] == 1 && a["isbalance"])
							: false)
							?true:false);																			
				};
			}	
			else if (tipe == 5)
			{
				var cek = function(a){									
					return ( (increaseon == "debet"? a["increaseon"] == 0 && !a["isbalance"] : false) ||					
							(increaseon != "debet"? a["increaseon"] == 0 && a["isbalance"] && a["exchangable"] : false)
							?true:false);																			
				};
			}
			else if (tipe == 6)
			{
				var cek = function(a){									
					return ( (increaseon == "debet"? a["increaseon"] == 1 && a["isbalance"] : false) ||					
							(increaseon != "debet"? a["increaseon"] == 0 && a["isbalance"] : false)
							?true:false);																			
				};
			}													
			else if (tipe == 7)
			{
				var cek = function(a){									
					return ( (increaseon == "debet"? a["increaseon"] == 0 && a["isbalance"] && a["exchangable"] : false) ||					
							(increaseon != "debet"? a["increaseon"] == 1 && !a["isbalance"] : false)
							?true:false);																			
				};
			}
			
			$(".transaction-"+increaseon+"-account").each(function(i,d){
				var n = $(d).attr("id").replace("w0","");				
				//$("#w0"+n).attachOptions();
			});	
			
			var usedaccounts = [];
			$(".transaction-"+increaseon+"-account option").each(function(i,d){
				
				var dval = $(d).val();
				if ($(d).prop("selected"))
				{
					usedaccounts[dval] = true;		
					//console.log(dval,usedaccounts);				
				}										
			});			
			
			
			$(".transaction-"+increaseon+"-account option").each(function(i,d){
				
				var dval = $(d).val();
				for (n in accounts) {
					var a = accounts[n];						
					if (a["id"]	== dval)
					{						
						var isuse = false;
						if ($(d).prop("selected"))
						{
							//usedaccounts[a["id"]] = true;	
							isuse = true;
						}											
						
						//console.log(usedaccounts);
						//console.log(a["id"]);
						if (cek(a) && ((typeof usedaccounts[a["id"]] == "undefined") || isuse) )
						{
						//	console.log(a["id"],false);
							$(d).prop("disabled",false);
							$(d).attr("class","show");
						}
						else
						{
							//console.log($(d),true);
							$(d).prop("disabled",true);
							$(d).attr("class","hidden");
							if ($(d).prop("selected"))
							{
								var p = $(d).parent();
								p.val(false);	
								var n = p.attr("id").replace("w0","");
								
								//$("#w0"+n).attachOptions();
										
								var select2_x = {"allowClear":true,"width":"resolve","theme":"krajee","placeholder":"<?= Yii::t('app','Select an account...') ?>"};
								jQuery.when(jQuery("#w0"+n).select2(select2_x)).done(initSelect2Loading("w0"+n));
								jQuery("#w0"+n).on("select2-open", function(){
									initSelect2DropStyle("w0"+n);				
								});
								
								usedaccounts = [];	
							}
						}

					}
					
				}
			});
			
			
			$(".transaction-"+increaseon+"-account").each(function(i,d){
				
				var n = $(d).attr("id").replace("w0","");
								
				//$("#w0"+n).detachOptions('.hidden');												
				
				var select2_x = {"allowClear":true,"width":"resolve","theme":"krajee","placeholder":"<?= Yii::t('app','Select an account...') ?>"};
				jQuery.when(jQuery("#w0"+n).select2(select2_x)).done(initSelect2Loading("w0"+n));
				jQuery("#w0"+n).on("select2-open", function(){
					initSelect2DropStyle("w0"+n);				
				});
			});
			
		}
		
		function renderFormDetails(increaseon,defval,journal)
		{	
			var xhr = $("#template_form_details").html();
														
			var n = $(".detail").length;
			
			$(".detail").each(function(){
				var n0 = $(this).attr("id").replace("detail_","");
				if (n0 != ":N")
				{
					n = Math.max(n,parseInt(n0));
				}
			});
			//xhr = xhr.replace(/w0/g,"w0:N").replace(/w1/g,"w1:N").replace(/w2/g,"w1:N").replace(/:N/g,n);
			xhr = xhr.replace(/:T/g,increaseon).replace(/:N/g,n);
			$("."+increaseon).append(xhr);						
			
			
			$("#w0"+n+"").val(false);
			if (typeof journal !== "undefined")
			{
				$("#w0"+n+"").val(typeof journal["account_id"] !== "undefined"?journal["account_id"]:false);
			}
			$("#w0"+n+"").unbind("change");
			$("#w0"+n+"").bind("change",function(){
				
				var tipe = $("#transaction-type").val();			
				filterOptions(tipe,"debet");
				filterOptions(tipe,"credit");															
			});
			
			var select2_x = {"allowClear":true,"width":"resolve","theme":"krajee","placeholder":"<?= Yii::t('app','Select an account...') ?>"};			
			jQuery("#w0"+n).prepend("<option val></option>");
			jQuery.when(jQuery("#w0"+n).select2(select2_x)).done(initSelect2Loading("w0"+n));
			jQuery("#w0"+n).on("select2-open", function(){
				initSelect2DropStyle("w0"+n);				
			});						
									
			
			var yapMoney = {"radixPoint":"<?= $module->currency["decimal_separator"]?>","groupSeparator":"<?= $module->currency["thousand_separator"]?>", "digits": 2,"autoGroup": true,"prefix":""};
			$("#w1"+n+"-disp").inputmask("decimal",yapMoney);
			$("#w1"+n+"-disp").change(function(){
				var val = parseFloat($("#w1"+n+"-disp").val().replace(yapMoney["prefix"],"").replace(/\<?= $module->currency["thousand_separator"]?>/g,"").replace(/\<?= $module->currency["decimal_separator"]?>/g,"."));
				val = (isNaN(val)?0:val);				
				$("#w1"+n).val(val);						
			});	 												
			
			$("#w1"+n+"-disp,#w1"+n+"").val(1.0);
			if (typeof journal !== "undefined")
			{
				$("#w1"+n+"-disp,#w1"+n+"").val(typeof journal["quantity"] !== "undefined"?journal["quantity"]:1);				
			}
			
			var total = (typeof defval !== "undefined"?defval:$("#transaction-total").val());			
			
			var yapMoney = {"radixPoint":"<?= $module->currency["decimal_separator"]?>","groupSeparator":"<?= $module->currency["thousand_separator"]?>", "digits": 2,"autoGroup": true,"prefix":""};
			$("#w2"+n+"-disp").inputmask("decimal",yapMoney);
			$("#w2"+n+"-disp").change(function(){				
				var val = parseFloat($("#w2"+n+"-disp").val().replace(yapMoney["prefix"],"").replace(/\<?= $module->currency["thousand_separator"]?>/g,"").replace(/\<?= $module->currency["decimal_separator"]?>/g,"."));
				val = (isNaN(val)?0:val);						
				$("#w2"+n).val(val);				
				$("#w2"+n).trigger("change");				
			});	
			
			$("#w2"+n).change(function(){										
				accountAmount(increaseon);	
			});
			
			$("#w2"+n+"-disp,#w2"+n+"").val(total);
			if (typeof journal !== "undefined")
			{
				$("#w2"+n+"-disp,#w2"+n+"").val(typeof journal["amount"] !== "undefined"?journal["amount"]:total);
			}						
						
			if (typeof journal !== "undefined")
			{
				$("#w3"+n+"").val(typeof journal["remarks"] !== "undefined"?journal["remarks"]:null);
			}
			
			$("#w4"+n+"").val(increaseon == "debet"?0:1);									
						
			var tipe = $("#transaction-type").val();			
			filterOptions(tipe,"debet");
			filterOptions(tipe,"credit");														
		}	
		
		function accountAmount(increaseon,istotal)
		{						
			
			var maxA= $("#transaction-total").val();
			var dA = 0;
			var lA = 0;
			var lD = false;
			
			var dId = "";
			
			$(".transaction-"+increaseon+"-amount").each(function(){
								
				
				if (typeof istotal !== "undefined")
				{
					if (typeof $(this).attr("data-ratio") !== "undefined")
					{
						var num = $(this).attr("data-ratio")*maxA;
						var dval = (Math.round(num * 100) / 100).toFixed(capprec);
						$(this).val(dval);	
					}						
				}				
				
				var A = parseFloat($(this).val());																
				
				var nA = ((dA+A) > maxA?(maxA-dA):A);
				$("#"+$(this).attr("id")+"-disp").val(nA);								
				
				dA += nA;				
				
				lA = nA;
				lD = $(this).attr("id");
								
				if (A == 0 || nA == 0)
				{					
					var id = $(this).attr("id").replace("w2","");
					dId += (dId == ""?"#":",#")+"detail_"+id;																				
				}								
			});	
			
			/*
			$(".transaction-"+increaseon+"-amount").each(function(){				
				var A = parseFloat($(this).val());
				
				if (A == 0)
				{
					var id = $(this).attr("id").replace("w2","");
					dId += (dId == ""?"#":",#")+"detail_"+id;					
					//console.log($("#w0"+id).val());
					//delete usedaccounts[parseInt($("#w0"+id).val())];						
					
				}								
			});																
			*/
											
			$(dId).css("display","none");
			$(dId).html("");
			
			if (dA < maxA)
			{
				renderFormDetails(increaseon,maxA-dA);
			}	
			
			var tipe = $("#transaction-type").val();			
			filterOptions(tipe,increaseon);			
		}				
		
		for (i in journals)
		{
			var j = journals[i];								
			renderFormDetails(j["type"] == 0?"debet":"credit",j["amount"],j);
		}
		
		function stopRKey(evt) { 
		  var evt = (evt) ? evt : ((event) ? event : null); 
		  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null); 
		  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;} 
		} 

		document.onkeypress = stopRKey; 											
		
<?php $this->endBlock(); ?>


<?php $this->beginBlock('JS_READY') ?>
   
<?php $this->endBlock(); ?>

</script>
<?php
yii\web\YiiAsset::register($this);
$this->registerJs($this->blocks['JS_END'], yii\web\View::POS_END);
$this->registerJs($this->blocks['JS_READY'], yii\web\View::POS_READY);
