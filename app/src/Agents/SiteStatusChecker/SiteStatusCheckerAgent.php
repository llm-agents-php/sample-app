<?php

declare(strict_types=1);

namespace App\Agents\SiteStatusChecker;

use LLM\Agents\Agent\Agent;
use LLM\Agents\Agent\AgentAggregate;
use LLM\Agents\Solution\MetadataType;
use LLM\Agents\Solution\Model;
use LLM\Agents\Solution\SolutionMetadata;
use LLM\Agents\Solution\ToolLink;

final class SiteStatusCheckerAgent extends AgentAggregate
{
    public const NAME = 'site_status_checker';

    public static function create(): self
    {
        $agent = new Agent(
            key: self::NAME,
            name: 'Site Status Checker',
            description: 'This agent specializes in checking the online status of websites. It can verify if a given URL is accessible, retrieve basic information about the site, and provide insights on potential issues if a site is offline.',
            instruction: 'You are a website status checking assistant. Your primary goal is to help users determine if a website is online and provide relevant information about its status. Use the provided tools to check site availability, retrieve DNS information, and perform ping tests when necessary. Always aim to give clear, concise responses about a site\'s status and offer potential reasons or troubleshooting steps if a site appears to be offline.',
        );

        $aggregate = new self($agent);

        $aggregate->addMetadata(
        // Instructions
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'describe_decisions',
                content: 'Before calling any tools, describe the decisions you are making and why you are making them.',
            ),
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'check_availability_first',
                content: 'Always start by checking the site\'s availability before using other tools.',
            ),
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'don_not_repeat',
                content: 'Don\'t repeat yourself. If you have already provided something, don\'t repeat it unless necessary.',
            ),
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'offline_site_checks',
                content: 'If a site is offline, consider checking DNS information and performing a ping test to gather more data.',
            ),
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'explain_technical_terms',
                content: 'Provide clear explanations of technical terms and status codes for users who may not be familiar with them.',
            ),
            new SolutionMetadata(
                type: MetadataType::Memory,
                key: 'suggest_troubleshooting',
                content: 'Suggest common troubleshooting steps if a site appears to be offline.',
            ),

            // Prompts examples
            new SolutionMetadata(
                type: MetadataType::Prompt,
                key: 'google',
                content: 'Check if google.com is online.',
            ),

            new SolutionMetadata(
                type: MetadataType::Prompt,
                key: 'offline_site',
                content: 'Can you check why buggregator.dev is offline?',
            ),

            new SolutionMetadata(
                type: MetadataType::Configuration,
                key: 'max_tokens',
                content: 3000,
            ),
        );

        $model = new Model(model: 'gpt-4o-mini');
        $aggregate->addAssociation($model);

        $aggregate->addAssociation(new ToolLink(name: CheckSiteAvailabilityTool::NAME));
        $aggregate->addAssociation(new ToolLink(name: GetDNSInfoTool::NAME));
        $aggregate->addAssociation(new ToolLink(name: PerformPingTestTool::NAME));

        return $aggregate;
    }
}
