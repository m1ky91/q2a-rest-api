package integration;

import business.q2a.Tag;
import integration.q2a.client.TagsClientResource;

import java.util.ArrayList;

public class Q2ATagDAO {
	public ArrayList<Tag> findAll() throws DAOException {
		TagsClientResource tags = new TagsClientResource();
		ArrayList<Tag> tagList = null;

		try {
			tagList = tags.represent();
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		return tagList;
	}
}
