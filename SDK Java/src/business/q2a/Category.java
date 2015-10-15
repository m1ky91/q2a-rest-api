package business.q2a;

import java.io.Serializable;

public class Category implements Serializable {

	private static final long serialVersionUID = 1L;

	private Integer categoryid;
	private String title;
	private Integer qcount;

	public Integer getCategoryid() {
		return categoryid;
	}

	public void setCategoryid(Integer categoryid) {
		this.categoryid = categoryid;
	}

	public String getTitle() {
		return title;
	}

	public void setTitle(String title) {
		this.title = title;
	}

	public Integer getQcount() {
		return qcount;
	}

	public void setQcount(Integer qcount) {
		this.qcount = qcount;
	}

}
