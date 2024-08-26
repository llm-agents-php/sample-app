# Agent Domain Layer Diagram

This mermaid diagram provides a visual representation of the Agent domain layer, showcasing the main classes,
interfaces, and their relationships. Here's a brief explanation of the diagram:

AgentInterface is the core interface that defines the contract for agents.
AgentAggregate implements AgentInterface and aggregates an Agent instance along with other Solution objects.
Agent, Model, and ToolLink all inherit from the abstract Solution class.
Solution has a composition relationship with SolutionMetadata.
AgentExecutor is responsible for executing agents and depends on various interfaces and classes.
AgentExecutorBuilder is used to build and configure AgentExecutor instances.
AgentFactoryInterface, AgentRepositoryInterface, and AgentRegistryInterface are interfaces for creating, retrieving, and
registering agents, respectively.

```mermaid
classDiagram
    class AgentInterface {
        <<interface>>
        +getKey() string
        +getName() string
        +getDescription() string
        +getInstruction() string
        +getTools() array
        +getModel() Model
        +getMemory() array
        +getPrompts() array
        +getConfiguration() array
    }

    class AgentAggregate {
        -agent: Agent
        -associations: array
        +addAssociation(Solution)
        +addMetadata(SolutionMetadata)
    }

    class Agent {
        +key: string
        +name: string
        +description: string
        +instruction: string
        +isActive: bool
    }

    class Solution {
        <<abstract>>
        +uuid: Uuid
        +name: string
        +type: SolutionType
        +description: string
        -metadata: array
        +addMetadata(SolutionMetadata)
        +getMetadata() array
    }

    class SolutionMetadata {
        +type: MetadataType
        +key: string
        +content: string|Stringable|int
    }

    class Model {
        +model: OpenAIModel
    }

    class ToolLink {
        +getName() string
    }

    class AgentExecutor {
        -llm: PipelineInterface
        -promptGenerator: AgentPromptGenerator
        -tools: ToolRepositoryInterface
        -agents: AgentRepositoryInterface
        -schemaMapper: SchemaMapper
        +execute(string, string|Stringable|ChatPrompt, Context, MessageCallback, array) Execution
    }

    class AgentExecutorBuilder {
        -executor: AgentExecutor
        -prompt: ChatPrompt
        -agentKey: string
        -sessionContext: array
        -callback: MessageCallback
        +withCallback(MessageCallback) self
        +withPrompt(ChatPrompt) self
        +withAgentKey(string) self
        +withSessionContext(array) self
        +withMessage(MessagePrompt) self
        +ask(string|Stringable) Execution
        +continue() Execution
    }

    class AgentFactoryInterface {
        <<interface>>
        +create() AgentInterface
    }

    class AgentRepositoryInterface {
        <<interface>>
        +get(string) AgentInterface
        +has(string) bool
    }

    class AgentRegistryInterface {
        <<interface>>
        +register(AgentInterface)
        +all() iterable
    }

    AgentAggregate ..|> AgentInterface
    AgentAggregate o-- Agent
    AgentAggregate o-- Solution
    Agent --|> Solution
    Model --|> Solution
    ToolLink --|> Solution
    AgentExecutor --> AgentRepositoryInterface
    AgentExecutor --> ToolRepositoryInterface
    AgentExecutorBuilder o-- AgentExecutor
    AgentFactoryInterface ..> AgentInterface
    AgentRegistryInterface ..> AgentInterface
    AgentRepositoryInterface ..> AgentInterface
    Solution o-- SolutionMetadata
```
