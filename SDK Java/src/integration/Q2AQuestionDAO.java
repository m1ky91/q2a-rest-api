package integration;

import business.q2a.Question;
import integration.q2a.client.QuestionsClientResource;

import java.util.ArrayList;

public class Q2AQuestionDAO {
	
	public ArrayList<Question> findAll() throws DAOException {
		QuestionsClientResource questions = new QuestionsClientResource();
		ArrayList<Question> questionList = null;

		try {
			questionList = questions.represent();
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		return questionList;
	}
	
	public Integer create(Question bean) throws DAOException {
		QuestionsClientResource questions = new QuestionsClientResource();
		Question question = null;

		try {
			question = questions.add(bean);
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		return question.getQuestionid();
	}

}
