package integration.q2a.client;

import business.q2a.User;
import integration.q2a.AbstractEndpoint;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.util.ArrayList;

import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.HttpClientBuilder;
import org.json.JSONArray;

public class UsersClientResource extends AbstractEndpoint{
	
	private HttpClient client = HttpClientBuilder.create().build();

	public UsersClientResource() {
		super("users");
	}
	
	public ArrayList<User> represent() throws Exception {
		ArrayList<User> users = new ArrayList<User>();
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
				User user = new User();
				user.setUserid((Integer) JSONresponse.getJSONObject(i).get("userid"));
				user.setHandle((String) JSONresponse.getJSONObject(i).get("handle"));
				user.setQcount((Integer) JSONresponse.getJSONObject(i).get("qcount"));
				user.setAcount((Integer) JSONresponse.getJSONObject(i).get("acount"));
				
				users.add(user);
			}
		}

		return users;
	}

}
