//------------------------------------------------------------------------------
// <auto-generated>
//     This code was generated from a template.
//
//     Manual changes to this file may cause unexpected behavior in your application.
//     Manual changes to this file will be overwritten if the code is regenerated.
// </auto-generated>
//------------------------------------------------------------------------------

namespace LMS.Models
{
    using System;
    using System.Collections.Generic;
    
    public partial class ConsolidatedLGDCalculated
    {
        public int id { get; set; }
        public string Segment { get; set; }
        public int CountOfAccount { get; set; }
        public double ExposureAtDefault { get; set; }
        public double Recoveries { get; set; }
        public double Costs { get; set; }
        public double EconomicRecoveries { get; set; }
        public double EconomicCosts { get; set; }
        public double RecoveryPercent { get; set; }
        public double EconomicRecoveryPercent { get; set; }
        public double LGD { get; set; }
        public double EconomicLGD { get; set; }
        public string by_user { get; set; }
    }
}
