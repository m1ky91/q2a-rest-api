package integration.q2a.client;

import business.q2a.Question;
import integration.q2a.AbstractEndpoint;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.util.ArrayList;

import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.HttpClientBuilder;
import org.json.JSONArray;
import org.json.JSONObject;

public class QuestionsClientResource extends AbstractEndpoint {

	private HttpClient client = HttpClientBuilder.create().build();

	public QuestionsClientResource() {
		super("questions");
	}

	public ArrayList<Question> represent() throws Exception {
		ArrayList<Question> questions = new ArrayList<Question>();
		HttpGet request = new HttpGet(getPath());
				
		HttpResponse response = client.execute(request);
		int responseCode = response.getStatusLine().getStatusCode();
		
		if (responseCode == 200) {
			BufferedReader rd = new BufferedReader(new InputStreamReader(response
					.getEntity().getContent()));

			StringBuffer result = new StringBuffer();
			String line = "";
			while ((line = rd.readLine()) != null) {
				result.append(line);
			}
			
			JSONArray JSONresponse = new JSONArray(result.toString());
			
			for (int i = 0; i < JSONresponse.length(); ++i) {
				Question question = new Question();
				question.setQuestionid((Integer) JSONresponse.getJSONObject(i).get("questionid"));
				question.setTitle((String) JSONresponse.getJSONObject(i).get("title"));
				question.setContent((String) JSONresponse.getJSONObject(i).get("content"));
				question.setCategoryid((Integer) JSONresponse.getJSONObject(i).get("categoryid"));
				
				JSONArray tagListJSON = (JSONArray) JSONresponse.getJSONObject(i).get("tags");

				ArrayList<String> tagList = new ArrayList<String>();    
				if (tagList != null) 
					for (int j = 0; j < tagListJSON.length(); ++j)  
						tagList.add(tagListJSON.get(j).toString());


				question.setTags(tagList);
				question.setUserid((Integer) JSONresponse.getJSONObject(i).get("userid"));
				question.setCreationdate((String) JSONresponse.getJSONObject(i).get("creationdate"));
				question.setAcount((Integer) JSONresponse.getJSONObject(i).get("acount"));
				
				questions.add(question);
			}
		}

		return questions;
	}

	public Question add(Question bean) throws Exception {
		Question question = new Question();		
		HttpPost request = new HttpPost(getPath());
		
		JSONObject obj = new JSONObject();
		obj.put("title", bean.getTitle());
		obj.put("content", bean.getContent());
		if (bean.getCategoryid() == null)
			obj.put("categoryid", JSONObject.NULL);
		else
			obj.put("categoryid", bean.getCategoryid());

		JSONArray tags = new JSONArray();
		if (bean.getTags() != null)
			for (int i = 0; i < bean.getTags().size(); ++i)
				tags.put(bean.getTags().get(i));
		obj.put("tags", tags);
		
		StringEntity params = new StringEntity(obj.toString(), "UTF-8");
		params.setContentType("application/json; charset=UTF-8");
		request.setEntity(params);

		HttpResponse response = client.execute(request);
		int responseCode = response.getStatusLine().getStatusCode();
		
		if (responseCode == 200) {
			BufferedReader rd = new BufferedReader(new InputStreamReader(response
					.getEntity().getContent()));

			StringBuffer result = new StringBuffer();
			String line = "";
			while ((line = rd.readLine()) != null) {
				result.append(line);
			}
			
			JSONArray JSONresponse = new JSONArray(result.toString());
			question.setQuestionid((Integer) JSONresponse.getJSONObject(0).get("questionid"));
			question.setTitle((String) JSONresponse.getJSONObject(0).get("title"));
			question.setContent((String) JSONresponse.getJSONObject(0).get("content"));
			question.setCategoryid((Integer) JSONresponse.getJSONObject(0).get("categoryid"));
			
			JSONArray tagListJSON = (JSONArray) JSONresponse.getJSONObject(0).get("tags");

			ArrayList<String> tagList = new ArrayList<String>();    
			if (tagList != null) 
				for (int j = 0; j < tagListJSON.length(); ++j)  
					tagList.add(tagListJSON.get(j).toString());


			question.setTags(tagList);
			question.setUserid((Integer) JSONresponse.getJSONObject(0).get("userid"));
			question.setCreationdate((String) JSONresponse.getJSONObject(0).get("creationdate"));
			question.setAcount((Integer) JSONresponse.getJSONObject(0).get("acount"));
		}

		return question;
	}

}
