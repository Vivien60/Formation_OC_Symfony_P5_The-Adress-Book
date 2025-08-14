<?php
declare(strict_types=1);
namespace infra;

class CommandParser
{
    const string COMMAND_PATTERN = "/^(.+?)\s*(?:(?<=\s)(.*))?$/s";
    const string ARGS_SEPARATOR_PATTERN = '/,/';
    public readonly bool $parseError;
    private bool $valid;
    private string $command = "";
    private ?array $args = null;
    private ?array $parsedArgs = null;

    public function __construct(private string $line)
    {
    }

    public function build() : static
    {
        $this->lexer();
        $this->parser();
        return $this;
    }

    public function validate() : bool
    {
        return !$this->parseError && $this->check();
    }

    private function lexer(): array
    {
        preg_match(static::COMMAND_PATTERN, $this->line, $matches);
        $this->command = trim($matches[1]);
        $this->args = isset($matches[2])? preg_split(static::ARGS_SEPARATOR_PATTERN, $matches[2]) : [];
        return array($this->command, $this->args);
    }

    private function parser()
    {
        switch ($this->command) {
            case "list" :
                $this->parsedArgs = [];
                break;
            case "detail" :
                $this->parsedArgs = [$this->parseId(), ...array_map('trim',array_slice($this->args, 1))];
                break;
            case "create" :
                $this->parsedArgs = $this->args;
                break;
            case "update" :
                $this->parsedArgs = [$this->parseId(), ...array_map('trim',array_slice($this->args, 1))];
                break;
            case "delete" :
                $this->parsedArgs = [$this->parseId(), ...array_slice($this->args, 1)];
                break;
            default:
                $this->parsedArgs = [];
                $this->parseError = true;
            return;
        }
        $this->parseError = false;
    }

    /**
     * @return int|null
     */
    protected function parseId(): ?int
    {
        return (isset($this->args[0]) && is_numeric($this->args[0]))? intval($this->args[0]) : null;
    }

    private function check() : bool
    {
        $this->valid = match ($this->command) {
            "list" => true,
            "detail" => count($this->parsedArgs) == 1 && $this->parsedArgs[0] !== null,
            "create" => count($this->parsedArgs) == 3,
            "update" => count($this->parsedArgs) == 4 && $this->parsedArgs[0] !== null,
            "delete" => count($this->parsedArgs) == 1 && $this->parsedArgs[0] !== null,
            default => false,
        };

        return $this->valid;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getArgs(): ?array
    {
        return $this->parsedArgs;
    }
}