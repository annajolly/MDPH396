var app = angular.module('QuestionnaireApp', ['ngRoute'])
.service('sharedData', function () {
        var doctorSerNum = 0;
        var backTo = "";
        var cameFrom = "";
        var successPublish = false;
        var questionnaireOptions = [];
        var questionnaireSerNum = -1;
        var questionnaireName = "";
        var questionnairePrivacy = -1;
        var questionnaireTags = [];
        var questionBank = [];
        var questionnaire = {
          id: -1,
          name: ""
        };
        var patient = {};
        var patientQuestionnaire = {};
        return {
          getBackTo: function() {
            return backTo;
          },
          setBackTo: function(value) {
            backTo = value;
          },
          getCameFrom: function() {
            return cameFrom;
          },
          setCameFrom: function(value) {
            cameFrom = value;
          },
          getSuccessPublish: function() {
            return successPublish;
          },
          setSuccessPublish: function(value) {
            successPublish = value;
          },
          getDoctorSerNum: function() {
            return doctorSerNum;
          },
          setDoctorSerNum: function(value) {
            doctorSerNum = value;
          },
          getQuestionnaireOptions: function() {
            return questionnaireOptions;
          },
          setQuestionnaireOptions: function(value) {
              questionnaireOptions = value;
          },
          getQuestionnaireSerNum: function() {
              return questionnaireSerNum;
          },
          setQuestionnaireSerNum: function(value) {
              questionnaireSerNum = value;
          },
          getQuestionnaireName: function() {
              return questionnaireName;
          },
          setQuestionnaireName: function(value) {
              questionnaireName = value;
          },
          getQuestionnairePrivacy: function () {
              return questionnairePrivacy;
          },
          setQuestionnairePrivacy: function(value) {
              questionnairePrivacy = value;
          },
          getQuestionnaireTags: function () {
              return questionnaireTags;
          },
          setQuestionnaireTags: function(value) {
              questionnaireTags = value;
          },
          getQuestionBank: function () {
              return questionBank;
          },
          setQuestionBank: function(value) {
              questionBank = value;
          },
          getQuestionnaire: function () {
              return questionnaire;
          },
          setQuestionnaire: function(value) {
              questionnaire = value;
          },
          getPatient: function () {
              return patient;
          },
          setPatient: function(value) {
              patient = value;
          },
          getPatientQuestionnaire: function () {
              return patientQuestionnaire;
          },
          setPatientQuestionnaire: function(value) {
              patientQuestionnaire = value;
          }
        };
    });
app.config(function ($routeProvider) {
  $routeProvider
    .when('/', {
      controller: 'HomeController',
      templateUrl: 'views/home.html'
    })
  	.when('/create/', {
          controller: 'CreateController',
    			templateUrl: 'views/create.html'
    })
    .when('/questionbank/', {
          controller: 'QuestionBankController',
          templateUrl: 'views/question-bank.html'
    })
    .when('/questionnaires/', {
          controller: 'QuestionnairesController',
    			templateUrl: 'views/questionnaires.html'
    })
    .when('/questionnaires/read/', {
          controller: 'ReadController',
    			templateUrl: 'views/read.html'
    })
    .when('/questionnaires/edit/', {
          controller: 'EditController',
    			templateUrl: 'views/edit.html'
    })
    .when('/patients/', {
          controller: 'PatientsController',
    			templateUrl: 'views/patients.html'
    })
    .when('/patients/patient', {
          controller: 'PatientController',
    			templateUrl: 'views/patient.html'
    })
    .when('/patients/patient/questionnaire/', {
          controller: 'PatientQuestionnaireController',
    			templateUrl: 'views/patient-questionnaire.html'
    })
    .otherwise({
      redirectTo: '/'
    });
});
