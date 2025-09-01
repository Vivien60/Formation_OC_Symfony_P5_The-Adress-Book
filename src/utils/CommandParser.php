<?php
declare(strict_types=1);
namespace utils;

/**
 * CommandParser is responsible for parsing a given input string, aka the command written by the user,
 * into a command and its associated arguments.
 * It performs lexical and syntactic analysis to validate and extract structured command data.
 */
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

    /**
     * Main method
     * Builds the command and its arguments from the input string,
     * by lexing (extract and type information globally)
     * then parsing to type each arg separately, based on the command found.
     * @return $this
     */
    public function build() : static
    {
        $this->lexer();
        $this->parser();
        return $this;
    }

    /**
     * Checks if the command is valid and if the arguments are valid.
     * @return bool
     */
    public function validate() : bool
    {
        return !$this->parseError && $this->check();
    }

    /**
     * Extracts the command and its arguments from the input string.
     * @return array
     */
    private function lexer(): array
    {
        preg_match(static::COMMAND_PATTERN, $this->line, $matches);
        $this->command = trim($matches[1]);
        $this->args = isset($matches[2])? preg_split(static::ARGS_SEPARATOR_PATTERN, $matches[2]) : [];
        return array($this->command, $this->args);
    }

    /**
     * Parses the arguments of the command based on the command found.
     * @return void
     */
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
     * Parses the ID argument of the command.
     * Returns null if the ID is not a valid integer.
     * @return int|null
     */
    protected function parseId(): ?int
    {
        return (isset($this->args[0]) && is_numeric($this->args[0]))? intval($this->args[0]) : null;
    }

    /**
     * Checks if the command and its arguments are valid,
     * based on the command found.
     * @return bool
     */
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

    /**
     * Returns arguments modified by the parser,
     * so they are typed based on the command found
     * @return array
     */
    public function getParsedArgs(): array
    {
        return $this->parsedArgs;
    }

    public function getCommand(): string
    {
        return $this->command;
    }


    /**
     * Returns arguments modified by the parser,
     * so they are typed based on the command found
     * @return array
     */
    public function getArgs(): ?array
    {
        return $this->parsedArgs;
    }
}