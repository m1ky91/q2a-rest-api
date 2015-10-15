package integration.q2a;

public class AbstractEndpoint {
	
	private final static String endpoint = "http://yourq2awebsite/api/v1/";

    private String path;
    
    public AbstractEndpoint(String basePath) {
        this.path = endpoint + basePath;
    }

    public String getPath() {
		return path;
	}

}
