import LocalizedStrings from "react-localization";

const LanguageStrings = new LocalizedStrings({
  en: {
    close: "Close",
    submitting: "Submitting...",
    submitQuestion: "Submit Question",
    dataSubmitted: "Data has been submitted",
    total: "Total",
    point: "Point",
    points: "Points",
    detail: "Detail",
    askQuestion: "Ask Question",
    haveQuestion: "I Have Questions",
    your: "Your {0}",
    name: "Name",
    email: "email",
    question: "Question",
    reset: "Reset"
  },
  zh: {
    close: "关闭",
    submitting: "数据提交中...",
    dataSubmitted: "数据提交成功！",
    submitQuestion: "提交问题",
    total: "总共",
    point: "分",
    points: "分",
    detail: "详情",
    askQuestion: "提问",
    haveQuestion: "我有疑问",
    your: "您的{0}",
    name: "名字",
    email: "电子邮箱",
    question: "问题",
    reset: "重新开始"
  }
});

export default LanguageStrings;
