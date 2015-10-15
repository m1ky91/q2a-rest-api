package integration.q2a.client;

import business.q2a.Tag;
import integration.q2a.AbstractEndpoint;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.util.ArrayList;

import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.HttpClientBuilder;
import org.json.JSONArray;

public class TagsClientResource extends AbstractEndpoint {

	private HttpClient client = HttpClientBuilder.create().build();

	public TagsClientResource() {
		super("tags");
	}

	public ArrayList<Tag> represent() throws Exception {
		ArrayList<Tag> tags = new ArrayList<Tag>();
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
				Tag tag = new Tag();
				tag.setTagid((Integer) JSONresponse.getJSONObject(i).get("tagid"));
				tag.setTitle((String) JSONresponse.getJSONObject(i).get("title"));
				tag.setTagcount((Integer) JSONresponse.getJSONObject(i).get("tagcount"));
								
				tags.add(tag);
			}
		}

		return tags;
	}

}


