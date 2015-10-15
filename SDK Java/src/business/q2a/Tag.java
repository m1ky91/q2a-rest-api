package business.q2a;

import java.io.Serializable;

public class Tag implements Serializable {

	private static final long serialVersionUID = 1L;

	private Integer tagid;
	private String title;
	private Integer tagcount;

	public Integer getTagid() {
		return tagid;
	}

	public void setTagid(Integer tagid) {
		this.tagid = tagid;
	}

	public String getTitle() {
		return title;
	}

	public void setTitle(String title) {
		this.title = title;
	}

	public Integer getTagcount() {
		return tagcount;
	}

	public void setTagcount(Integer tagcount) {
		this.tagcount = tagcount;
	}
}
