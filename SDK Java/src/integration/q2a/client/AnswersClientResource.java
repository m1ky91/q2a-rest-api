package integration.q2a.client;

import business.q2a.Answer;
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

public class AnswersClientResource extends AbstractEndpoint {

	private HttpClient client = HttpClientBuilder.create().build();

	public AnswersClientResource() {
		super("answers");
	}

	public ArrayList<Answer> represent() throws Exception {
		ArrayList<Answer> answers = new ArrayList<Answer>();
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
				Answer answer = new Answer();
				answer.setAnswerid((Integer) JSONresponse.getJSONObject(i).get("answerid"));
				answer.setContent((String) JSONresponse.getJSONObject(i).get("content"));
				answer.setQuestionid((Integer) JSONresponse.getJSONObject(i).get("questionid"));
								
				answers.add(answer);
			}
		}

		return answers;
	}

	public Answer add(Answer bean) throws Exception {
		Answer answer = new Answer();
		HttpPost request = new HttpPost(getPath());
		
		JSONObject obj = new JSONObject();
		obj.put("content", bean.getContent());
		obj.put("questionid", bean.getQuestionid());
		
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
			answer.setAnswerid((Integer) JSONresponse.getJSONObject(0).get("answerid"));
			answer.setContent((String) JSONresponse.getJSONObject(0).get("content"));
			answer.setQuestionid((Integer) JSONresponse.getJSONObject(0).get("questionid"));
		}

		return answer;
	}

}

