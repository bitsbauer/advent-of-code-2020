using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;

namespace Day8
{
    class Program
    {
        public struct Instruction
        {
            public readonly int Position { get; init; }
            public readonly string Operation { get; init; }
            public readonly int Argument { get; init; }
            public Instruction(int position, string operation, int argument)
            {
                Position = position;
                Operation = operation;
                Argument = argument;
            }
        }

        static void Main(string[] args)
        {
            Console.WriteLine("Advent of Code 2020 - Day 8");

            var instructions = InitInstructions("input.txt");

            Console.WriteLine("Part 1: Run your copy of the boot code.\nImmediately before any instruction is executed a second time, what value is in the accumulator?");
            var result = RunInstructions(instructions);
            Console.WriteLine("Result: " + result.Item2);

            Console.WriteLine("Part 2: Fix the program so that it terminates normally by changing exactly one jmp (to nop) or nop (to jmp). What is the value of the accumulator after the program terminates?");
            result = RunInstructionsUntilTerminate(instructions);
            Console.WriteLine("Result: " + result.Item2);
        }
        
        static IEnumerable<string> ReadFrom(string file)
        {
            string line;
            using (var reader = File.OpenText(file))
            {
                while ((line = reader.ReadLine()) != null)
                {
                    yield return line;
                }
            }
        }

        static List<Instruction> InitInstructions(string file)
        {
            var instructions = new List<Instruction>();
            int position = 0;
            foreach(var line in ReadFrom(file)) {
                var parts = line.Split(" ");
                instructions.Add(
                    new Instruction(position++, parts[0], int.Parse(parts[1]))
                );
            }
            return instructions;
        }

        static Tuple<bool, int> RunInstructions(List<Instruction> instructions)
        {
            var executions = instructions.Select(x => 0).ToList();
            var index = 0;
            var accumulator = 0;
            var terminated = false;
            while (executions[index] == 0) {
                executions[index] += 1;
                switch(instructions[index].Operation) {
                    case "acc":
                        accumulator += instructions[index].Argument;
                        index ++;
                        break;
                    case "jmp":  
                        index += instructions[index].Argument;
                        break;
                    case "nop":
                        index ++;
                        break;
                }
                if (index >= instructions.Count) {
                    terminated = true;
                    break;
                }
            }
            return new Tuple<bool, int>(terminated, accumulator);
        }

        static Tuple<bool, int> RunInstructionsUntilTerminate(List<Instruction> instructions)
        {
            var instructionsToCheck = instructions.Where(x => x.Operation != "acc").ToList();
            foreach(var instructionToCheck in instructionsToCheck) {
                var modifiedInstructions = instructions.Select(x => x).ToList();
                var newOperation = instructionToCheck.Operation == "jmp" ? "nop" : "jmp";
                modifiedInstructions[instructionToCheck.Position] = new Instruction(instructionToCheck.Position, newOperation, instructionToCheck.Argument);
                var result = RunInstructions(modifiedInstructions);
                if(result.Item1) {
                    return result;
                }
            }
            throw new Exception("Could not find terminating instructions");
        }
    }
}
