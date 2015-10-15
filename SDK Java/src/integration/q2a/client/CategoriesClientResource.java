package integration.q2a.client;

import business.q2a.Category;
import integration.q2a.AbstractEndpoint;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.util.ArrayList;

import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.HttpClientBuilder;
import org.json.JSONArray;

public class CategoriesClientResource extends AbstractEndpoint {

	private HttpClient client = HttpClientBuilder.create().build();

	public CategoriesClientResource() {
		super("categories");
	}

	public ArrayList<Category> represent() throws Exception {
		ArrayList<Category> categories = new ArrayList<Category>();
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
				Category category = new Category();
				category.setCategoryid((Integer) JSONresponse.getJSONObject(i).get("categoryid"));
				category.setTitle((String) JSONresponse.getJSONObject(i).get("title"));
				category.setQcount((Integer) JSONresponse.getJSONObject(i).get("qcount"));
								
				categories.add(category);
			}
		}

		return categories;
	}

}

