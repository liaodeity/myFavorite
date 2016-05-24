var PK10 = function(input){
	//var buyType = '4单 1倍 元';// 测试输入的例子  1名235610  200倍 元
	var buyType = input;
	var typeAll = ['大','小','单','双'];
	var buyTypeArr = buyType.split(' ');
	var numTh = numType = numMultiple = moneyType = null;
	buyTypeArr.forEach(function(ele,index){
		switch(index){
			case 0: 
				if(ele.indexOf('名')!==false){
					numTh = ele.substr(0,1);
					if(ele.length==7){
						numType = ele.substr(2,6);
					}else if(ele.length==8){
						numType = ele.substr(2,7);
					}

				}else{

					numTh = ele.substr(0,1); 
					numType = ele.substr(1,1);
					
					alert(numType);
				}
				break;
			case 1: // 倍数
				numMultiple = ele.replace('倍','');
				break;
			case 2: 
				moneyType = ele; 
				if(moneyType!='元'){
					alert('不是元！');
					return false;
				}
				break;
		}
	});
	// 先清空之前选的号码
	$('#num-select .pp').eq(numTh-1).find('input[value="清"]').click();
	console.log(numTh+'-'+numType+'-'+numMultiple);
	var curVal = 0;
	if(numType.length>2){
		for(var i=0;i<numType.length;i++){
			if(typeof(numType[i])=='undefined' || numType[i]=='0')continue;
			curVal = numType[i];
			if(i>0 && numType[i] == '1'){
				curVal = 10;
				console.log('has changed to 10!');
			}
			$('#num-select .pp').eq(numTh-1).find('input.code').eq(curVal-1).click();
		}
	}else{
		$('#num-select .pp').eq(numTh-1).find('input[value="'+numType+'"]').click();
	}
	// 填写倍数
	$('#game-dom .beiBox #beishu').val(numMultiple);
	

	return true;
}
// 选好了
var chooseComplete = function(){
	gameActionAddCode();
	return;
}
// 确认购买
var confirmBuy = function(){
	$('#btnPostBet').click();
	setTimeout(function(){
		//$('.ui-dialog-buttonset button').eq(0).click();
	},200);
	return;
}

//PK10('9名235610 1倍 元');
PK10('10双 1倍 元');
chooseComplete();
confirmBuy();
