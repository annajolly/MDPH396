URL:

Home:				/
Create:				/create/
View Questionnaires:		/questionnaires/
View Questionnaire:		/questionnaires/read/
Edit Questionnaire:		/questionnaires/edit/
Question Bank:			/questionbank/
View Patients:			/patients/
View Patient's Questionnaires:	/patients/patient
View Patient's Questionnaire:	/patients/patient/questionnaire


VIEWS:

home.html
 |--- create.html
 |--- questionnaires.html
	|--- read.html
	|--- edit.html
 |--- question-bank.html
 |--- patients.html
	|--- patient.html
		|--- patient-questionnaire.html

CONTROLLERS:

HomeController
 |--- CreateController
 |--- QuestionBankController
 |--- QuestionnairesController
	|--- ReadController
	|--- EditController
 |--- PatientsController
 	|--- PatientController
  		|--- PatientQuestionnaireController

