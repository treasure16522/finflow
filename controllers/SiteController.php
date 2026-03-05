<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\SignupForm;
use app\models\ContactForm;
use app\models\User;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
{
    return [
        'access' => [
            'class' => AccessControl::class,
            'only' => ['logout', 'signup', 'request-password-reset', 'reset-password'],
            'rules' => [
                [
                    'actions' => ['signup', 'login', 'request-password-reset', 'reset-password'],
                    'allow' => true,
                    'roles' => ['?'],
                ],
                [
                    'actions' => ['logout'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ],
        'verbs' => [
            'class' => VerbFilter::class,
            'actions' => [
                //'logout' => ['post'],
            ],
        ],
    ];
}

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['login']);
        }
        return $this->redirect(['dashboard/index']);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $this->layout = false; // Don't use main layout for login page
        
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['dashboard/index']);
        }

        $model = new LoginForm();
        
        // Pre-fill with demo data for quick testing
        $model->email = 'alex@example.com';
        $model->password = 'password123';
        $model->rememberMe = true;

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['dashboard/index']);
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Signup action.
     *
     * @return Response|string
     */
    public function actionSignup()
    {
        $this->layout = false; // Don't use main layout for signup page
        
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['dashboard/index']);
        }

        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please login.');
            return $this->redirect(['login']);
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['login']);
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');
            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Displays profile page.
     *
     * @return string
     */
    public function actionProfile()
    {
        $this->layout = 'main';
        return $this->render('profile');
    }
    /**
 * Requests password reset.
 *
 * @return Response|string
 */
public function actionRequestPasswordReset()
{
    $this->layout = false;
    
    $model = new \app\models\PasswordResetRequestForm();
    
    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
        if ($model->sendEmail()) {
            Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
            return $this->redirect(['login']);
        } else {
            Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
        }
    }
    
    return $this->render('request-password-reset', [
        'model' => $model,
    ]);
}

/**
 * Resets password.
 *
 * @param string $token
 * @return Response|string
 * @throws \yii\base\InvalidParamException
 */
public function actionResetPassword($token)
{
    $this->layout = false;
    
    try {
        $model = new \app\models\ResetPasswordForm($token);
    } catch (\Exception $e) {
        Yii::$app->session->setFlash('error', $e->getMessage());
        return $this->redirect(['login']);
    }
    
    if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
        Yii::$app->session->setFlash('success', 'New password saved.');
        return $this->redirect(['login']);
    }
    
    return $this->render('reset-password', [
        'model' => $model,
    ]);
}
}