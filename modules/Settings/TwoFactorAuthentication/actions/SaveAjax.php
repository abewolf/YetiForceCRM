<?php

class Settings_TwoFactorAuthentication_SaveAjax_Action extends Settings_Vtiger_Basic_Action
{
	/**
	 * {@inheritdoc}
	 */
	public function checkPermission(\App\Request $request)
	{
		$currentUserModel = \App\User::getCurrentUserModel();
		if ($currentUserModel->isAdmin()) {
			return true;
		} else {
			throw new \App\Exceptions\AppException('LBL_PERMISSION_DENIED');
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function process(\App\Request $request)
	{
		$methods = $request->getByType('methods', 'Alnum');
		if (!in_array($methods, Users_Totp_Authmethod::ALLOWED_USER_AUTHY_MODE)) {
			throw new \App\Exceptions\IllegalValue('ERR_NOT_ALLOWED_VALUE||' . $methods, 406);
		}
		$config = new \App\Configurator('security');
		$config->set('USER_AUTHY_TOTP_NUMBER_OF_WRONG_ATTEMPTS', $request->getInteger('number_wrong_attempts'));
		$config->set('USER_AUTHY_MODE', $methods);
		$config->set('USER_AUTHY_TOTP_EXCEPTIONS', $request->getArray('users', 'Integer'));
		$config->save();
		$response = new Vtiger_Response();
		$response->setResult([
			'success' => true,
			'message' => \App\Language::translate('LBL_SAVE_CONFIG', $request->getModule(false))
		]);
		$response->emit();
	}
}
