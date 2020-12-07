using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;

namespace Day5
{
    class Program
    {
        static void Main(string[] args)
        {
            Console.WriteLine("Advent of Code 2020 - Day 7");

            var rules = GetRules();     

            Console.WriteLine("Part 1: How many bag colors can eventually contain at least one shiny gold bag?");
            var containingBags = new List<string>();
            CountContainingBags(rules, "shiny gold", containingBags);
            Console.WriteLine("Result: " + containingBags.Distinct().Count());

            Console.WriteLine("Part 2: How many individual bags are required inside your single shiny gold bag?");
            Console.WriteLine("Result: " + CountSubBags(rules, "shiny gold"));
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

        static Dictionary<string, Dictionary<string, int>> GetRules()
        {
            return ReadFrom("input.txt")
                .Where(x => ! x.Contains("no other"))
                .Select(x => x.TrimEnd('.').Split(" bags contain "))
                .ToDictionary(
                    x => x[0], 
                    x => x[1].Split(", ")
                        .ToDictionary(
                            x => x.Split(' ', 2)[1].Replace(" bags", "").Replace(" bag", ""), 
                            x => int.Parse(x.Split(' ', 2)[0])
                        )
                );
        }

        static void CountContainingBags(Dictionary<string, Dictionary<string, int>> rules, string searchColor, List<string> containingBags)
        {
            var bagColors = rules.Where(x => x.Value.ContainsKey(searchColor)).Select(x => x.Key).ToArray();
            foreach (var bagColor in bagColors) {
                containingBags.Add(bagColor);
                CountContainingBags(rules, bagColor, containingBags);
            }
        }

        static int CountSubBags(Dictionary<string, Dictionary<string, int>> rules, string searchColor)
        {
            var count = 0;
            if (rules.ContainsKey(searchColor))  {
                foreach(var bagType in rules[searchColor]) {
                    count += bagType.Value;
                    count += bagType.Value * CountSubBags(rules, bagType.Key);
                }
            }
            return count;
        }
    }
}
