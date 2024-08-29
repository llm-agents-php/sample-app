# LLM Agents Sample App

This sample application demonstrates the practical implementation and usage patterns of the LLM Agents library.

> For more information about the LLM Agents package and its capabilities, please refer to
> the [LLM Agents documentation](https://github.com/llm-agents-php/agents).

It provides a CLI interface to interact with various AI agents, showcasing the power and flexibility of the LLM Agents
package.

![image](https://github.com/user-attachments/assets/53104067-d3df-4983-8a59-435708f2b70c)

## Features

- Multiple pre-configured AI agents with different capabilities
- CLI interface for easy interaction with agents
- Integration with OpenAI's GPT models
- Database support for session persistence

## Prerequisites

- PHP 8.3 or higher
- Composer
- Git
- OpenAI API key

## Quick Start with Docker

The easiest way to run the app is using our pre-built Docker image.

**Follow these steps to get started:**

1. Make sure you have Docker installed on your system.

2. Run the Docker container with the following command:

```bash
docker run --name chat-app -e OPENAI_KEY=<your_api_key> ghcr.io/llm-agents-php/sample-app:1.0.0
```

or if you want to get environment variables from a file:

```bash
docker run --name chat-app --env-file .env ghcr.io/llm-agents-php/sample-app:1.0.0
```

and `.env` file should look like this:

```bash
OPENAI_KEY=your_api_key_here
```

> Replace `<your_api_key>` with your OpenAI API key.

3. Once the container is running, you can interact with the app using the following command:

## Usage

### Chatting with Agents

To start a chat session with an AI agent:

1. Run the following command:

**Using docker container**
```bash
docker exec -it chat-app php app.php chat
```

**Using local installation**
```bash
php app.php chat
```

2. You will see a list of available agents and their descriptions. Choose the desired agent by entering its number.

![image](https://github.com/user-attachments/assets/3cd223a8-3ab0-4879-9e85-83539c93003f)

3. After selecting an agent, you will see a message like this:

![image](https://github.com/user-attachments/assets/0d18ca6c-9ee9-4942-b383-fc42abf18bc7)

```bash
************************************************************
*     Run the following command to see the AI response     *
************************************************************

php app.php chat:session <session_uuid> -v
```

**Using docker container**
```bash
docker exec -it chat-app php app.php chat:session <session_uuid> -v
````
> Replace `<session_uuid>` with the actual session UUID.


4. Copy the provided command and run it in a new terminal tab. This command will show the AI response to your message.

![image](https://github.com/user-attachments/assets/1dfdfdd1-f69d-44af-afb2-807f9fa2da84)

## Available CLI Commands

The sample app provides several CLI commands for interacting with agents and managing the application:

- `php app.php agent:list`: List all available agents
- `php app.php tool:list`: List all available tools
- `php app.php chat`: Start a new chat session
- `php app.php chat:session <session-id>`: Continue an existing chat session
- `php app.php migrate`: Execute database migrations

Use the `-h` or `--help` option with any command to see more details about its usage.

## Available Agents

The sample app comes with several pre-configured agents, each designed for specific tasks:

### Site Status Checker

- **Key**: `site_status_checker`
- **Description**: This agent specializes in checking the online status of websites. It can verify if a given URL is
  accessible, retrieve basic information about the site, and provide insights on potential issues if a site is
  offline.
- **Capabilities**:
    - Check site availability
    - Retrieve DNS information
    - Perform ping tests
    - Provide troubleshooting steps for offline sites

### Order Assistant

- **Key**: `order_assistant`
- **Description**: This agent helps customers with order-related questions. It can retrieve order information, check
  delivery status, and provide customer support for e-commerce related queries.
- **Capabilities**:
    - Retrieve order numbers
    - Check delivery dates
    - Access customer profiles
    - Provide personalized assistance based on customer age and preferences

### Smart Home Control Assistant

- **Key**: `smart_home_control`
- **Description**: This agent manages and controls various smart home devices across multiple rooms, including
  lights, thermostats, and TVs.
- **Capabilities**:
    - List devices in specific rooms
    - Control individual devices (turn on/off, adjust settings)
    - Retrieve device status and details
    - Suggest energy-efficient settings

### Code Review Agent

- **Key**: `code_review`
- **Description**: This agent specializes in reviewing code. It can analyze code files, provide feedback, and
  suggest improvements.
- **Capabilities**:
    - List files in a project
    - Read file contents
    - Perform code reviews
    - Submit review comments

### Task Splitter

- **Key**: `task_splitter`
- **Description**: This agent analyzes project descriptions and breaks them down into structured task lists with
  subtasks.
- **Capabilities**:
    - Retrieve project descriptions
    - Create hierarchical task structures
    - Assign task priorities
    - Generate detailed subtasks


## Dev installation

1. Clone the repository:

```bash
git clone https://github.com/llm-agents-php/sample-app.git
cd sample-app
```

2. Install dependencies:

```bash
composer install
```

3. Set up the environment:

```bash
cp .env.sample .env
```

Open the `.env` file and add your OpenAI API key:

```bash
OPENAI_KEY=your_api_key_here
```

4. Initialize the project:

```bash
make init
```

This command will download and set up all required binaries, including:

- Dolt: A SQL database server
- RoadRunner: A high-performance PHP application server

### Starting the Server

To start the RoadRunner server and the Dolt database, run:

```bash
./rr serve
```

## Knowledge Base

This sample project includes a console command to generate a knowledge base, which can be useful for creating project
documentation or training data for AI models like Claude.

### Creating a Project in Claude

Follow these steps to create a new project in Claude using the generated knowledge base:

1. Create a New Project
    - Go to the Claude interface (e.g., chat.openai.com for ChatGPT).
    - Create a new project.

2. Add Instructions from README below

3. Upload Knowledge Base Files
    - Locate the `./knowledge-base` directory on your local machine.
    - Upload all files from this directory to Claude.
    - Ensure all relevant PHP files, documentation, and any other project-related files are included.

4. Test Your Project
    - To test if everything is set up correctly, ask Claude to create a "Weather Checker" agent.
    - Review the generated code and explanations provided by Claude.

### Instructing an AI with

Once you've generated the knowledge base, you can use it to create new agent codebases or to provide context for
AI-assisted development. Here's an example of how you might use the generated knowledge base to instruct an AI:

```prompt
Create a new AI agent with the following specifications:

1. Agent Name: [Provide a descriptive name for the agent]
2. Agent Unique Key: [Provide a unique identifier for the agent, using lowercase letters, numbers, and underscores]
3. Agent Description: [Provide a detailed description of the agent's purpose, capabilities, and use cases]
4. Agent Instruction: [Provide a detailed instruction for the agent, explaining how it should behave, what its primary goals are, and any specific guidelines it should follow]
5. Tools: List the tools that would be useful for this agent. For each tool, provide:
   a. Tool Key: [A unique identifier for the tool]
   b. Tool Description: [A concise yet comprehensive explanation of the tool's functionality]
   c. Tool Input Schema: [Describe the input parameters for the tool in JSON format]

Example Tool Format:
{
    "key": "example_tool",
    "description": "This tool performs X function, useful for Y scenarios. It takes A and B as inputs and returns Z.",
    "input_schema": {
        "type": "object",
        "properties": {
            "param1": {
                "type": "string",
                "description": "Description of param1"
            },
            "param2": {
                "type": "integer",
                "description": "Description of param2"
            }
        },
        "required": ["param1", "param2"]
    }
}

6. Agent Memory: [List any specific information or guidelines that the agent should always keep in mind]
7. Agent example prompts
8. Always use gpt-4o-mini model as a bae model for the agent

Your tasks:
* Generate all necessary PHP classes for Agent
	* Agent
	* AgentFactory
	* All necessary tools
	* All necessary Tool input shemas
- You use PHP 8.3 with Constructor property promotion, named arguments, and do not use annotations.
```

By providing such instructions along with the generated knowledge base, you can guide AI models like Claude to create
new components that align with your project's structure and coding standards.

### Generating the Knowledge Base

To generate the knowledge base, run the following command:

```bash
php app.php kb:generate
```

This command will create a knowledge base in the `./knowledge-base` directory. The generated knowledge base contains
documentation and codebase examples that can be used, for instance, to create a project for Claude AI.

### Extending the Knowledge Base

As your project grows, you may want to update the knowledge base to include new features, agents, or tools. Simply run
the `kb:generate` command again to refresh the knowledge base with the latest changes in your project.

This approach allows for an iterative development process where you can continuously improve and expand your agent
ecosystem, leveraging both human expertise and AI assistance.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This sample app is open-source software licensed under the MIT license.
