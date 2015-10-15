# Question2Answer REST API v. 0.1
REST API for Question2Answer http://www.question2answer.org/ 

Install the plugin and read documentation at http://yourq2awebsite/api

Use SDK Java changing endpoint on the class AbstractEndpoint in src/integration/q2a/client.
Important dependencies:

		<dependency>
			<groupId>org.apache.httpcomponents</groupId>
			<artifactId>httpclient</artifactId>
			<version>4.5</version>
		</dependency>
		<dependency>
			<groupId>org.json</groupId>
			<artifactId>json</artifactId>
			<version>20141113</version>
		</dependency>

N.B. The Web API for the moment does not provide a level of security. For internal security uncomment their rows in the source code of plugin.

For any issues contact me: micheledichio@gmail.com
