package business.q2a;

import java.io.Serializable;

public class Answer implements Serializable {

	private static final long serialVersionUID = 1L;

	private Integer answerid;
	private Integer questionid;
	private String content;

	public Integer getAnswerid() {
		return answerid;
	}

	public void setAnswerid(Integer answerid) {
		this.answerid = answerid;
	}

	public Integer getQuestionid() {
		return questionid;
	}

	public void setQuestionid(Integer questionid) {
		this.questionid = questionid;
	}

	public String getContent() {
		return content;
	}

	public void setContent(String content) {
		this.content = content;
	}

}
