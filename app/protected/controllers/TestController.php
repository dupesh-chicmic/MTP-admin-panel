<?php
class TestController extends Controller
{
	public function actionTestRI()
	{
		$t=new RIVehicleData();
		//var_export($t->vehicleFreeReport('05CW3106'));
		var_export($t->queryVehicle('05CW3106'));
		var_export($t->getLastSoapMessage());
	}

	public function actionViewRealexTpl()
	{
		$this->renderPartial('viewRealexTpl');
	}
}