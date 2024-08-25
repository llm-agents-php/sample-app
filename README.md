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

## Installation

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

## Usage

### Starting the Server

To start the RoadRunner server and the Dolt database, run:

```bash
./rr serve
```

### Running Migrations

After starting the server, run the database migrations:

```bash
php app.php migrate
```

### Chatting with Agents

To start a chat session with an AI agent:

1. Run the following command:

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

php app.php chat:session 01918b72-6433-71bd-8116-c1d76175a241 -v
```

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

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This sample app is open-source software licensed under the MIT license.
